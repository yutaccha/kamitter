<?php

namespace App\Console\Commands;

use App\AutomaticLike;
use App\Http\Components\TwitterApi;
use App\SystemManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoLike extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:like';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic favorite with Twitter API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    const API_URL_SEARCH = 'search/tweets';
    const API_URL_LIKE = 'favorites/create';

    //API側の上限は900/1D
    const API_REQUEST_RATE_PER_DAY = 700;
    const DO_API_PER_A_DAY = 24;
    const INTERVAL_HOURS = 1;

    /**
     * Execute the console command.
     * 登録された自動いいねのフィルターワードごとにツイート検索を行う
     * 検索にヒットしたツイートに対していいねする
     * API実行回数を設定して上限回数に達しないようにしている
     * @return mixed
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('AutoLike : 開始');
        Log::info('=====================================================================');

        //runのレコードを取得する
        //稼動中のステータスになっているauto_like_statusのレコードを取得する
        $auto_like_running_status_list = SystemManager::where('auto_like_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_like_running_status_list as $auto_like_running_status_item) {
            $system_manager_id = $auto_like_running_status_item->id;
            $twitter_user_id = $auto_like_running_status_item->twitter_user_id;
            Log::info('#system_manager_id : ', [$system_manager_id]);
            Log::info('#twitter_user_id : ' , [$twitter_user_id]);

            //ユーザーごとのいいね条件配列を取得
            $auto_like_list = AutomaticLike::where('twitter_user_id', $twitter_user_id)
                ->with('twitterUser', 'filterWord')->get();

            $auto_like_list_quantity = count($auto_like_list);

            //いいね条件ごとに検索
            foreach ($auto_like_list as $auto_like) {
                $flg_skip_to_next_user = false;

                //検索にヒットしたツイート配列を取得
                $api_result = $this->fetchGetTweetListApi($auto_like, $auto_like_list_quantity);
                $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
                if ($flg_skip_to_next_user === true) {
                    Log::notice('#APIエラーのため次のユーザーにスキップ');
                    break;
                }
                //取得したツイート一覧に対していいねをする
                foreach ($api_result->statuses as $item) {
                    $like_target_id = $item->id_str;
                    $api_result = $this->fetchLikeApi($auto_like, $like_target_id);
                    $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
                    if ($flg_skip_to_next_user === true) {
                        Log::notice('#APIエラーのため次のユーザーにスキップ');
                        break 2;
                    }

                }

            }
        }

        Log::info('=====================================================================');
        Log::info('AutoLike : 終了');
        Log::info('=====================================================================');

    }


    /**
     * APIを使用して、フィルターワードで指定されたワードでツイート検索を行う
     * @param $auto_like
     * @param $auto_like_list_quantity
     * @return array|object
     */
    private function fetchGetTweetListApi($auto_like, $auto_like_list_quantity)
    {
        Log::info('##API ツイートリスト取得開始');

        //APIに必要な変数の用意
        $count = self::API_REQUEST_RATE_PER_DAY / self::DO_API_PER_A_DAY / $auto_like_list_quantity;
        $query = $auto_like->filterWord->getMergedWordStringForQuery();
        Log::info('##いいねする数: ', [$count]);
        Log::info('##検索クエリ: ', [$query]);

        $token = $auto_like->twitterUser->token;
        $token_secret = $auto_like->twitterUser->token_secret;
        $param = [
            'q' => $query,
            'count' => $count,
            'result_type' => 'recent',
            'include_entities' => false,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_SEARCH,
            $param, $token, $token_secret);

        Log::info('##API ツイートリスト取得完了');

        return $response_json;
    }


    /**
     * APIを使用して、いいねをする
     * @param $auto_like
     * @param $like_target_id
     * @return array|object
     */
    private function fetchLikeApi($auto_like, $like_target_id)
    {
        Log::debug('##API 自動いいね開始');

        //APIに必要な変数の用意
        $token = $auto_like->twitterUser->token;
        $token_secret = $auto_like->twitterUser->token_secret;

        $param = [
            'id' => $like_target_id,
            'include_entities' => false,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('POST', self::API_URL_LIKE,
            $param, $token, $token_secret);

        Log::debug('##API 自動いいね完了');
        return $response_json;
    }
}

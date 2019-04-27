<?php

namespace App\Console\Commands;

use App\AutomaticLike;
use App\Http\Components\TwitterApi;
use App\SystemManager;
use Illuminate\Console\Command;


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



    //サービスステータス
    const STATUS_STOP = 1;
    const STATUS_RUNNING = 2;
    const STATUS_WAIT_API_RESTRICTION = 3;

    const API_URL_SEARCH = 'search/tweets';
    const API_URL_LIKE = 'favorites/create';

    const API_REQUEST_RATE_PER_DAY = 600;
    const API_PER_A_DAY = 12;
    const INTERVAL_HOURS = 2;

    //APIエラーコード
    const ERROR_CODE_SUSPENDED = 63;
    const ERROR_CODE_LIMIT_EXCEEDED = 88;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //suspendを探す→時間を過ぎてればrunに治す

        //runのレコードを取得する
        //稼動中のステータスになっているauto_like_statusのレコードを取得する
        $auto_like_running_status_list = SystemManager::where('auto_like_status', self::STATUS_RUNNING)->get();

        foreach ($auto_like_running_status_list as $auto_like_running_status_item) {


            $system_manager_id = $auto_like_running_status_item->id;
            $twitter_user_id = $auto_like_running_status_item->twitter_user_id;

            //ユーザーごとのいいね条件配列を取得
            $auto_like_list = AutomaticLike::where('twitter_user_id', $twitter_user_id)
                ->with('twitterUser', 'filterWord')->get();

            $auto_like_list_quantity = count($auto_like_list);

            //いいね条件ごとに検索
            foreach ($auto_like_list as $auto_like) {
                $flg_skip_to_next_user = false;

                //検索にヒットしたツイート配列を取得
                $api_result = $this->fetchGetTweetListApi($auto_like, $auto_like_list_quantity);
                $flg_skip_to_next_user = $this->handleApiError($api_result, $system_manager_id);

                if ($flg_skip_to_next_user === true) {
                    break;
                }
                foreach ($api_result->statuses as $item) {
                    $like_target_id = $item->id_str;
                    $api_result = $this->fetchLikeApi($auto_like, $like_target_id);
                    $flg_skip_to_next_user = $this->handleApiError($api_result, $system_manager_id);
                    if ($flg_skip_to_next_user === true) {
                        break 2;
                    }

                }

            }


        }


    }


    private function handleApiError($api_result, $system_manager_id)
    {
        if (property_exists($api_result, 'errors')) {
            foreach ($api_result->errors as $error) {
                //アカウント凍結時の処理
                if ($error->code === self::ERROR_CODE_SUSPENDED) {
                    SystemManager::stopAllServices($system_manager_id);
                    echo 'send mail¥n';
                    return true;
                }
                //レート制限時の処理
                if ($error->code === self::ERROR_CODE_LIMIT_EXCEEDED) {
                    echo 'limit exceeded';
                    return true;
                }
            }

        }
        return false;
    }

    private function fetchGetTweetListApi($auto_like, $auto_like_list_quantity)
    {
        //APIに必要な変数の用意
        $count = self::API_REQUEST_RATE_PER_DAY / self::API_PER_A_DAY / $auto_like_list_quantity;

        $token = $auto_like->twitterUser->token;
        $token_secret = $auto_like->twitterUser->token_secret;
        $param = [
            'q' => $auto_like->filterWord->getMergedWordStringForQuery(),
            'count' => 2,
            'result_type' => 'recent',
            'include_entities' => false,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_SEARCH,
            $param, $token, $token_secret);

        return $response_json;
    }

    private function fetchLikeApi($auto_like, $like_target_id)
    {
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

        return $response_json;
    }
}

<?php

namespace App\Console\Commands;

use App\FollowerTarget;
use App\SystemManager;
use Illuminate\Console\Command;
use App\FollowTarget;
use App\TwitterUser;
use App\Http\Components\TwitterApi;
use App\FilterWord;

class AutoFollow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:follow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic follow with Twitter API';

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

    const API_URL_FOLLOWERS_LIST = 'followers/list';
    const API_URL_FOLLOW = 'friendships/create';

    const API_REQUEST_RATE_PER_DAY = 600;
    const API_PER_A_DAY = 12;
    const INTERVAL_HOURS = 2;

    //APIエラーコード
    const ERROR_CODE_SUSPENDED = 63;
    const ERROR_CODE_LIMIT_EXCEEDED = 88;

    const FOLLOW_RATE = [
        "100" => 20,
        "500" => 25,
        "1000" => 40,
        '1500' => 70,
        "2000" => 100,
        "3000" => 150,
    ];


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //runのレコードを取得する
        //稼動中のステータスになっているauto_follow_statusのレコードを取得する
        $auto_follow_running_status_list = SystemManager::where('auto_follow_status', self::STATUS_RUNNING)->get();

        foreach ($auto_follow_running_status_list as $auto_follow_running_status_item) {

            $system_manager_id = $auto_follow_running_status_item->id;
            $twitter_user_id = $auto_follow_running_status_item->twitter_user_id;

            //フォローターゲットリストを取得、設定されていなければ次のユーザーへ
            $follow_target_list = FollowTarget::where('twitter_user_id', $twitter_user_id)->first();
            if ($follow_target_list === null){
                echo 'こんて';
                continue;
            }


            //最後に作成されたフォロワーターゲットリストのカラムを見て
            //リストを作る必要があるかないかを判定する
            $follower_target_list_latest = FollowerTarget::where('twitter_user_id', $twitter_user_id)
                ->latest()->first();
            $twitter_api_follower_cursor =
                (!empty($follower_target_list_latest)) ? $follower_target_list_latest->cursor : false;

            //新しくフォロワーターゲットリストを作る必要がある、
            //または、フォロワーターゲットリスト作成途中の場合はフォロワーターゲットリストを作成する
            if ($twitter_api_follower_cursor === false || $twitter_api_follower_cursor !== '0') {
                $this->makeFollowerTargetList($system_manager_id, $twitter_user_id);
            }

            //フォロワーターゲットリストを取得
            $follower_target_list = FollowerTarget::where('twitter_user_id', $twitter_user_id)
                ->with('twitterUser')->get();

            //フォロワーターゲットリストがない場合は次のユーザーへスキップ
            if($follower_target_list === null){
                continue;
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



    private function makeFollowerTargetList($system_manager_id, $twitter_user_id)
    {
        $under_construction_status = 2;

        //ターゲットアカウントリストを取得
        $follow_target = FollowTarget::where('twitter_user_id', $twitter_user_id)
            ->with('filterWord')->first();
        if (empty($follow_target)){
            return;
        }
        $follow_target->status = $under_construction_status;
        $filter_word = $follow_target->filterWord;
        $follow_target->save();

        $target_screen = $follow_target->target;

        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();

        do {
            dd('before_api');
            //APIでフォロワーリストを取得
            $api_result = $this->fetchGetFollowerListApi($twitter_user, $target_screen);
            dd($api_result);
            //エラーチェック
            $flg_skip_to_next_user = $this->handleApiError($api_result, $system_manager_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            //上から検索
            $this->addToFollowList($api_result, $filter_word);


            //cursorが0
            $cursor = $api_result->next_cursor_str;
        } while ($cursor === "0");
    }

    private function fetchGetFollowerListApi($twitter_user, $target_screen)
    {
        $count = 10;

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'screen_name' => $target_screen,
            'count' => $count,
            'include_entities' => false,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_FOLLOWERS_LIST,
            $param, $token, $token_secret);

        return $response_json;
    }


    private function addToFollowList($api_result, $filter_word)
    {

        //日本人か？のチェック
        //ワードが入っているか
        //アンフォローリストにいないか

        //trueなら
        //ターゲットリストにいれる
    }

}

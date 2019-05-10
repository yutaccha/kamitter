<?php

namespace App\Console\Commands;

use App\Http\Components\TwitterApi;
use App\SystemManager;
use App\UnfollowTarget;
use App\TwitterUser;
use Illuminate\Console\Command;
use App\FollowHistory;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class InspectNotFollowback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspect:followback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect twitter user followback who I followed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    const API_URL_FRIEND_LOOKUP = 'friendships/lookup';
    const FOLLOWER_NUMBER_FOR_ENTRY_UNFOLLOW = 5000;

    /**
     * Execute the console command.
     * フォローから7日経過したユーザーデータを元に、
     * APIを使用してフォローリレーションを取得する
     * フォローバックされていないユーザーは、アンフォローターゲットリストに保存する
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('InspectFollowback : 開始');
        Log::info('=====================================================================');

        //runのレコードを取得する
        //稼動中のステータスになっているauto_unfollow_statusのレコードを取得する
        $auto_unfollow_running_status_list = SystemManager::where('auto_unfollow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_unfollow_running_status_list as $auto_unfollow_running_status_item) {
            $system_manager_id = $auto_unfollow_running_status_item->id;
            $twitter_user_id = $auto_unfollow_running_status_item->twitter_user_id;
            Log::info('#system_manager_id : ', [$system_manager_id]);
            Log::info('#twitter_user_id : ', [$twitter_user_id]);


            //現在フォロワー数の確認
            $follower = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);
            if ($this->isFollowerOverEntryNumber($follower)) {
                $this->changeAutoUnfollowStatusToStop($auto_unfollow_running_status_item);
                continue;
            }

            //フォローから７日経過したユーザーの取得
            $users_followed_7days_ago = $this->getUsersFollowed7daysAgo($twitter_user_id);
            //フォローバックバリデーション
            $this->addToUnfollowTargetsByCheckFollowback($system_manager_id, $twitter_user_id, $users_followed_7days_ago);

        }

        Log::info('=====================================================================');
        Log::info('InspectFollowback : 終了');
        Log::info('=====================================================================');
    }


    /**
     * SystemManagerのauto_unfollow_statusを停止状態にする
     * @param $system_manager
     */
    private function changeAutoUnfollowStatusToStop($system_manager)
    {
        $system_manager->auto_unfollow_status = SystemManager::STATUS_STOP;
        $system_manager->save();
    }


    /**
     * フォロワー数が稼動条件数を満たしていればtrueを返す
     * @param $follower
     * @return bool
     */
    private function isFollowerOverEntryNumber($follower)
    {
        if ($follower > self::FOLLOWER_NUMBER_FOR_ENTRY_UNFOLLOW) {
            return false;
        }
        return true;
    }


    /**
     * フォローから7日経過したユーザー一覧の取得
     * @param $twitter_user_id
     * @return mixed
     */
    private function getUsersFollowed7daysAgo($twitter_user_id)
    {
        $the_day_7_before = Carbon::today()->addDay(-7);
        $users = FollowHistory::where('twitter_user_id', $twitter_user_id)
            ->whereDate('created_at',  '=' , $the_day_7_before)->get();
        return $users;
    }


    /**
     *
     * @param $system_manager_id
     * @param $twitter_user_id
     * @param $users
     */
    private function addToUnfollowTargetsByCheckFollowback($system_manager_id, $twitter_user_id, $users)
    {
        Log::info('##フォローバックバリデーション開始');
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $user_id_string_list = $this->makeUsersStringList($users);
        foreach ($user_id_string_list as $user_id_string){
            //配列型でapiが帰ってくる
            //handleApiError内でproperty_existsを使用しているのでオブジェクトに変換する必要がある
            $api_result = (object)$this->fetchFollowbackInfo($twitter_user, $user_id_string);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            $this->inspectFollowback($api_result, $twitter_user_id);
        }

        Log::info('##フォローバックバリデーション完了');
    }


    /**
     * フォローバックしていないユーザーをアンフォローターゲットリストに追加する
     * @param $api_result
     * @param $twitter_user_id
     */
    private function inspectFollowBack($api_result, $twitter_user_id)
    {
        foreach ($api_result as $inspect_target){
            if (!in_array('followed_by', $inspect_target->connections)){
                $this->addUnfollowTargetDB($inspect_target, $twitter_user_id);
            }
        }
    }


    /**
     * ユーザーをアンフォローターゲットリストに追加する
     * @param $target
     * @param $twitter_user_id
     */
    private function addUnfollowTargetDB($target, $twitter_user_id){
        $unfollow_target = new UnfollowTarget();
        $unfollow_target->twitter_user_id = $twitter_user_id;
        $unfollow_target->twitter_id = $target->id_str;
        $unfollow_target->save();
    }


    /**
     * ['id,id,id,id,id'..., 'id,id,id,id,id...', ...]形式の文字列の配列を作成する
     * @param $users
     * @return array
     */
    private function makeUsersStringList($users)
    {
        $users_string_list = [];
        //全てのidを配列形式で取得する
        $user_id_strings = Arr::pluck($users, 'twitter_id');
        //id100件を含んだ配列をさらに新たな配列に格納する
        $users_id_strings_chunk = array_chunk($user_id_strings, 100);
        foreach ($users_id_strings_chunk as $user_id_string) {
            //id100件の配列を , カンマで接続した文字列に変換する
            $users_string_list[] = implode(',', $user_id_string);
        }

        return $users_string_list;
    }


    /**
     * APIを使ってフォローリレーション情報の取得を行う
     * @param $twitter_user
     * @param $user_id_string
     * @return array|object
     */
    private function fetchFollowbackInfo($twitter_user, $user_id_string)
    {
        Log::info('###API フォローリレーションの取得開始');

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id_string
        ];
        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_FRIEND_LOOKUP,
            $param, $token, $token_secret);


        Log::info('###API フォローリレーションの取得完了');
        return $response_json;
    }


    /**
     * APIを使用してツイッターのフォロワー数を取得する
     * @param $system_manager_id
     * @param $twitter_user_id
     * @return int
     */
    private function getTwitterFollowerNum($system_manager_id, $twitter_user_id)
    {
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $api_result = TwitterApi::fetchTwitterUserInfo($twitter_user);
        $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
        if ($flg_skip_to_next_user === true) {
            return 0;
        }

        return $api_result->followers_count;
    }
}

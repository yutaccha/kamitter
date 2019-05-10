<?php

namespace App\Console\Commands;

use App\FollowHistory;
use App\Http\Components\TwitterApi;
use App\SystemManager;
use App\TwitterUser;
use App\UnfollowInspect;
use App\UnfollowTarget;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;


class InspectActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspect:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect Active Twitter User. And add to UnfollowTargetList';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    const API_URL_USERS_LOOKUP = 'users/lookup';
    const FOLLOWER_NUMBER_FOR_ENTRY_UNFOLLOW = 5000;

    /**
     * Execute the console command.
     * フォロー履歴から、アクティブユーザーを検査するテーブルを作成する
     * 検査テーブルに入っているユーザーにバリデーションを行い
     * バリデーションに引っかかったユーザーをアンフォローターゲットリストにmoveする
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('InspectActiveUser : 開始');
        Log::info('=====================================================================');

        //runのレコードを取得する
        //稼動中のステータスになっているauto_unfollow_statusのレコードを取得する
        $auto_unfollow_running_status_list =
            SystemManager::where('auto_unfollow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_unfollow_running_status_list as $auto_unfollow_running_status_item) {
            $system_manager_id = $auto_unfollow_running_status_item->id;
            $twitter_user_id = $auto_unfollow_running_status_item->twitter_user_id;
            Log::info('#system_manager_id : ', [$system_manager_id]);
            Log::info('#twitter_user_id : ', [$twitter_user_id]);


            //現在のフォロワー数の確認
            $follower = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);
            if ($this->isFollowerOverEntryNumber($follower)) {
                $this->changeAutoUnfollowStatusToStop($auto_unfollow_running_status_item);
                Log::info('フォロワー数が5000人以下です。');
                Log::info('次のユーザーにスキップします');
                continue;
            }


            // 検査テーブルにユーザーがいれば検査する
            // 検査テーブルが0の場合、検査テーブルを作成する
            $unfollow_inspect_one = UnfollowInspect::where('twitter_user_id', $twitter_user_id)->first();
            if (is_null($unfollow_inspect_one)) {
                $this->copyFollowHistoryToUnfollowInspect($twitter_user_id);
            }
            $unfollow_inspects = UnfollowInspect::where('twitter_user_id', $twitter_user_id)->get();

            //アクティブユーザーバリデーション
            $this->addToUnfollowTargetsByCheckActiveUser($system_manager_id, $twitter_user_id, $unfollow_inspects);

        }

        Log::info('=====================================================================');
        Log::info('InspectActiveUser : 終了');
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
     * APIで検査テーブルのユーザーの最新ツイート情報を取得する
     * ツイートされた日にちからアクティブユーザーかどうかを判別して、
     * 非アクティブユーザーならアンフォローターゲットリストにmoveする
     * @param $system_manager_id
     * @param $twitter_user_id
     * @param $inspect_targets
     */
    private function addToUnfollowTargetsByCheckActiveUser($system_manager_id, $twitter_user_id, $inspect_targets)
    {
        Log::info('##アクティブユーザーバリデーション開始');

        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        // クエリで 'id,id,id,id,id'という文字列が使用されるので、文字列を生成する
        $user_id_string_list = $this->makeUsersStringList($inspect_targets);
        foreach ($user_id_string_list as $user_id_string) {
            $api_result = (object)$this->fetchActiveUserInfo($twitter_user, $user_id_string);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            //アクティブユーザーバリデーション
            $this->inspectActiveUser($api_result, $twitter_user_id);
            //検査したユーザーを検査テーブルから一括削除
            UnfollowInspect::where('twitter_user_id', $twitter_user_id)->limit(100)->delete();
        }
        Log::info('##アクティブユーザーバリデーション完了');
    }


    /**
     * 15日以上ツイートしていないユーザーをアンフォローターゲットDBにmoveする
     * @param $api_result
     * @param $twitter_user_id
     */
    private function inspectActiveUser($api_result, $twitter_user_id)
    {
        $before_15days = Carbon::now()->addDay(-15);
        foreach ($api_result as $inspect_target) {
            $last_tweet_date = Carbon::create($inspect_target->status->created_at);
            if ($last_tweet_date->lte($before_15days)) {
                $this->addUnfollowTargetDB($inspect_target, $twitter_user_id);
            }
        }
    }


    /**
     * unfollow_targetにユーザーを保存する
     * @param $target
     * @param $twitter_user_id
     */
    private function addUnfollowTargetDB($target, $twitter_user_id)
    {
        $unfollow_target = new UnfollowTarget();
        $unfollow_target->twitter_user_id = $twitter_user_id;
        $unfollow_target->twitter_id = $target->id_str;
        $unfollow_target->save();
    }


    /**
     * APIで検査テーブルのユーザーの情報を取得する
     * @param $twitter_user
     * @param $user_id_string
     * @return array|object
     */
    private function fetchActiveUserInfo($twitter_user, $user_id_string)
    {
        Log::info('###API ツイッターユーザーの情報取得開始');

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id_string
        ];
        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_USERS_LOOKUP,
            $param, $token, $token_secret);


        Log::info('###API ツイッターユーザーの情報取得完了');
        return $response_json;
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
     * フォローヒストリーのユーザーを検査テーブルにコピーする
     * @param $twitter_user_id
     */
    private function copyFollowHistoryToUnfollowInspect($twitter_user_id)
    {
        //フォローヒストリーをinspectにコピー
        $follow_histories = FollowHistory::where('twitter_user_id', $twitter_user_id)
            ->select('twitter_user_id', 'twitter_id')->get()->toArray();

        data_fill($follow_histories, '*.created_at', Carbon::now()->format('Y-m-d H:i:s'));
        data_fill($follow_histories, '*.updated_at', Carbon::now()->format('Y-m-d H:i:s'));
        UnfollowInspect::insert($follow_histories);
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

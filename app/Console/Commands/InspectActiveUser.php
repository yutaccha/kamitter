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
     *
     * @return mixed
     */
    public function handle()
    {
        //runのレコードを取得する
        //稼動中のステータスになっているauto_unfollow_statusのレコードを取得する
        $auto_unfollow_running_status_list =
            SystemManager::where('auto_unfollow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_unfollow_running_status_list as $auto_unfollow_running_status_item) {
            $system_manager_id = $auto_unfollow_running_status_item->id;
            $twitter_user_id = $auto_unfollow_running_status_item->twitter_user_id;

            $follower = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);
            if ($this->isFollowerOverEntryNumber($system_manager_id, $follower)) {
                $this->changeAutoUnfollowStatusToStop($auto_unfollow_running_status_item);
                continue;
            }

            $unfollow_inspect_one = UnfollowInspect::where('twitter_user_id', $twitter_user_id)->first();
            if (is_null($unfollow_inspect_one)) {
                $this->copyFollowHistoryToUnfollowInspect($twitter_user_id);
            }
            $unfollow_inspects = UnfollowInspect::where('twitter_user_id', $twitter_user_id)->get();

            $this->addToUnfollowTargetsByCheckActiveUser($system_manager_id, $twitter_user_id, $unfollow_inspects);

        }
    }


    private function changeAutoUnfollowStatusToStop($system_manager)
    {
        $system_manager->auto_unfollow_status = 1;
        $system_manager->save();
    }

    private function addToUnfollowTargetsByCheckActiveUser($system_manager_id, $twitter_user_id, $inspect_targets)
    {
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $user_id_string_list = $this->makeUsersStringList($inspect_targets);
        foreach ($user_id_string_list as $user_id_string) {
            $api_result = (object)$this->fetchActiveUserInfo($twitter_user, $user_id_string);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            $this->inspectActiveUser($api_result, $twitter_user_id);
            UnfollowInspect::where('twitter_user_id', $twitter_user_id)->limit(100)->delete();
        }
    }


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

    private function addUnfollowTargetDB($target, $twitter_user_id)
    {
        $unfollow_target = new UnfollowTarget();
        $unfollow_target->twitter_user_id = $twitter_user_id;
        $unfollow_target->twitter_id = $target->id_str;
        $unfollow_target->save();
    }


    private function fetchActiveUserInfo($twitter_user, $user_id_string)
    {
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id_string
        ];
        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_USERS_LOOKUP,
            $param, $token, $token_secret);

        return $response_json;
    }

    private function makeUsersStringList($users)
    {
        $users_string_list = [];
        $user_id_strings = Arr::pluck($users, 'twitter_id');
        $users_id_strings_chunk = array_chunk($user_id_strings, 100);
        foreach ($users_id_strings_chunk as $user_id_string) {
            $users_string_list[] = implode(',', $user_id_string);
        }

        return $users_string_list;
    }

    private function copyFollowHistoryToUnfollowInspect($twitter_user_id)
    {
        //フォローヒストリーをinspectにコピー
        $follow_histories = FollowHistory::where('twitter_user_id', $twitter_user_id)
            ->select('twitter_user_id', 'twitter_id')->get()->toArray();

        data_fill($follow_histories, '*.created_at', Carbon::now()->format('Y-m-d H:i:s'));
        data_fill($follow_histories, '*.updated_at', Carbon::now()->format('Y-m-d H:i:s'));
        UnfollowInspect::insert($follow_histories);
    }


    private function isFollowerOverEntryNumber($system_manager_id, $follower)
    {
        if ($follower > self::FOLLOWER_NUMBER_FOR_ENTRY_UNFOLLOW) {
            $system_manager = SystemManager::where('id', $system_manager_id)->first();
            $system_manager->auto_unfollow_status = SystemManager::STATUS_STOP;
            return false;
        }
        return true;
    }


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

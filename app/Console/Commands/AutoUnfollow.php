<?php

namespace App\Console\Commands;

use App\Http\Components\TwitterApi;
use App\SystemManager;
use App\TwitterUser;
use App\UnfollowTarget;
use Illuminate\Console\Command;
use App\UnfollowHistory;

class AutoUnfollow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:unfollow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic unfollow with Twitter API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    const API_URL_UNFOLLOW = 'friendships/destroy';
    const FOLLOWER_NUMBER_FOR_ENTRY_UNFOLLOW = 5000;
    const INTERVAL_HOURS = 1;
    const API_PER_A_DAY = 24 / self::INTERVAL_HOURS;
    const UNFOLLOW_RATE_MAX = 150;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $auto_unfollow_running_status_list = SystemManager::where('auto_unfollow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_unfollow_running_status_list as $auto_unfollow_running_status_item) {
            $system_manager_id = $auto_unfollow_running_status_item->id;
            $twitter_user_id = $auto_unfollow_running_status_item->twitter_user_id;

            $follower = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);
            if ($this->isFollowerOverEntryNumber($system_manager_id, $follower)) {
                $this->changeAutoUnfollowStatusToStop($auto_unfollow_running_status_item);
                continue;
            }

            $unfollow_targets = UnfollowTarget::where('twitter_user_id', $twitter_user_id)->get();
            $this->autoUnfollow($system_manager_id, $twitter_user_id, $unfollow_targets);
        }
    }


    private function changeAutoUnfollowStatusToStop($system_manager)
    {
        $system_manager->auto_unfollow_status = 1;
        $system_manager->save();
    }


    private function autoUnfollow($system_manager_id, $twitter_user_id, $unfollow_targets)
    {
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $unfollow_count = 0;
        $unfollow_limit = (int)(self::UNFOLLOW_RATE_MAX / self::API_PER_A_DAY);
        foreach ($unfollow_targets as $unfollow_target) {
            $api_result = (object)$this->fetchAutoUnfollow($twitter_user, $unfollow_target->twitter_id);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            $this->moveUnfollowTargetsToUnfollowHistories($twitter_user_id, $unfollow_target);

            $unfollow_count++;
            if ($unfollow_count >= $unfollow_limit){
                return;
            }
        }
    }

    private function moveUnfollowTargetsToUnfollowHistories($twitter_user_id, $unfollow_target)
    {
        $unfollow_history = new UnfollowHistory();
        $unfollow_history->twitter_user_id = $twitter_user_id;
        $unfollow_history->twitter_id = $unfollow_target->twitter_id;
        $unfollow_history->save();

        $unfollow_target->delete();
    }

    private function fetchAutoUnfollow($twitter_user, $user_id)
    {
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('POST', self::API_URL_UNFOLLOW,
            $param, $token, $token_secret);

        return $response_json;
    }

    private function isFollowerOverEntryNumber($system_manager_id, $follower)
    {
        if ($follower > self::FOLLOWER_NUMBER_FOR_ENTRY_UNFOLLOW) {
            $system_manager = SystemManager::find($system_manager_id);
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
        $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id);
        if ($flg_skip_to_next_user === true) {
            return 0;
        }

        return $api_result->followers_count;
    }
}

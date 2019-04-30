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
        $auto_unfollow_running_status_list = SystemManager::where('auto_unfollow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_unfollow_running_status_list as $auto_unfollow_running_status_item) {
            $system_manager_id = $auto_unfollow_running_status_item->id;
            $twitter_user_id = $auto_unfollow_running_status_item->twitter_user_id;

            $follower = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);
            if ($this->isFollowerOverEntryNumber($system_manager_id, $follower)) {
                continue;
            }

            $users_followed_7days_ago = $this->getUsersFollowed7daysAgo($twitter_user_id);
            $this->addToUnfollowTargets($system_manager_id, $twitter_user_id, $users_followed_7days_ago);

        }
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

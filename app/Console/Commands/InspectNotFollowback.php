<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\FollowerTarget;
use App\FollowTarget;
use App\Http\Components\TwitterApi;
use App\SystemManager;
use App\TwitterUser;
use App\FollowHistory;


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
            dd($follower);
        }

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

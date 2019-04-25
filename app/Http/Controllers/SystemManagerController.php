<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditSystemManager;
use App\SystemManager;

class SystemManagerController extends Controller
{
    public static function stopAllServices($id)
    {
        $system_manager = SystemManager::where('id', $id)->first();
        $system_manager->auto_follow_status = 1;
        $system_manager->auto_unfollow_status = 1;
        $system_manager->auto_like_status = 1;
        $system_manager->auto_tweet_status = 1;
        $system_manager->save();
    }


    public function run(EditSystemManager $request)
    {
        $twitter_user = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user)->first();
        if (!$system_manager) {
            abort(404);
        }
        switch ($request->type) {
            case 1:
                $system_manager->auto_follow_status = 2;
                break;
            case 2:
                $system_manager->auto_unfollow_status = 2;
                break;
            case 3:
                $system_manager->auto_like_status = 2;
                break;
            case 4:
                $system_manager->auto_tweet_status = 2;
                break;
        }
        $system_manager->save();
        return response($system_manager, 200);
    }

    public function stop(EditSystemManager $request)
    {
        $twitter_user = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user)->first();
        if (!$system_manager) {
            abort(404);
        }
        switch ($request->type) {
            case 1:
                $system_manager->auto_follow_status = 1;
                break;
            case 2:
                $system_manager->auto_unfollow_status = 1;
                break;
            case 3:
                $system_manager->auto_like_status = 1;
                break;
            case 4:
                $system_manager->auto_tweet_status = 1;
                break;
        }
        $system_manager->save();
        return response($system_manager, 200);
    }

    public function show()
    {
        $twitter_user = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user)->first();
        if (!$system_manager) {
            abort(404);
        }
        return response($system_manager, 200);
    }
}

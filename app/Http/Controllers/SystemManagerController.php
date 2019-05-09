<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditSystemManager;
use App\SystemManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class SystemManagerController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        // Controllerに認証を適応
        $this->middleware('auth');
    }


    public function run(EditSystemManager $request)
    {
        $user_id = Auth::id();
        if (is_null($user_id)) {
            abort(419);
        }
        $twitter_user_id = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user_id)->first();
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
        $user_id = Auth::id();
        if (is_null($user_id)) {
            abort(419);
        }
        $twitter_user_id = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user_id)->first();
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
        $user_id = Auth::id();
        if (is_null($user_id)) {
            abort(419);
        }
        $twitter_user_id = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user_id)->first();
        if (!$system_manager) {
            abort(404);
        }
        return response($system_manager, 200);
    }
}

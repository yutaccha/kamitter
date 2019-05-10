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

    /**
     * 指定されたサービスを稼働状態に変更する
     * @param EditSystemManager $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function run(EditSystemManager $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user_id)->first();
        if (is_null($system_manager)) {
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

    /**
     * 指定されたサービスを停止する
     * @param EditSystemManager $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function stop(EditSystemManager $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user_id)->first();
        if (is_null($system_manager)) {
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

    /**
     * TwitterUserが利用しているSystemManagerを返す
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show()
    {
        $twitter_user_id = session()->get('twitter_id');
        $system_manager = SystemManager::where('twitter_user_id', $twitter_user_id)->first();
        if (is_null($system_manager)) {
            abort(404);
        }
        return response($system_manager, 200);
    }
}

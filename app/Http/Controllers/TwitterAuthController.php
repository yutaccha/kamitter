<?php

namespace App\Http\Controllers;

use App\TwitterUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\SystemManager;


class TwitterAuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        // Controllerに認証を適応
        $this->middleware('auth');
    }

    /**
     * ユーザーをTwitterの認証ページにリダイレクトする
     *
     * @return Response
     */

    public function oauth()
    {
        return Socialite::driver('twitter')->redirect();
    }


    public function handleProviderCallback(Request $request)
    {
        $user_id = Auth::id();

        try {
            $auth_twitter_user = Socialite::driver('twitter')->user();
            $exist_twitter_user = DB::table('twitter_users')->where('token', $auth_twitter_user->token)->first();

            if (null !== $exist_twitter_user) {
                if (Auth::id() === $exist_twitter_user->user_id) {
                    $request->session()->put('twitter_id', $exist_twitter_user->id);
                }
                return redirect('/twitter');
            }

            $twitter_user = [
                'user_id' => $user_id,
                'token' => $auth_twitter_user->token,
                'token_secret' => $auth_twitter_user->tokenSecret,
            ];
            $new_twitter_user = TwitterUser::create($twitter_user);

            $system_manager = new SystemManager();
            $system_manager->user_id =$user_id;
            $system_manager->twitter_user_id = $new_twitter_user->id;
            $system_manager->save();


        } catch (Exception $e) {
            return abort(500);
        }
        $request->session()->put('twitter_id', $new_twitter_user->id);
        return redirect('/twitter');
    }


    public function getId()
    {
        return response(session()->get('twitter_id') ?? '', 200);
    }


    public function setId(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
        if (!$twitter_user) {
            return 404;
        }
        if ($twitter_user->user_id === $user_id) {
            session()->put('twitter_id', $id);
        } else {
            abort(403);
        }
        return response(200);
    }


    public function delete(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
        if (!$twitter_user) {
            return 404;
        }
        if ($twitter_user->user_id === $user_id) {
            $twitter_user->delete();
        } else {
            abort(403);
        }
        return response(200);
    }


    public function logout()
    {
        session()->forget('twitter_id');
        return response(200);
    }

}



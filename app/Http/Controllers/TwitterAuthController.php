<?php

namespace App\Http\Controllers;

use App\TwitterUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;


class TwitterAuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        // 認証が必要
        //indexは認証しなくても見れるようにする
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
        try {
            $auth_user = Socialite::driver('twitter')->user();
            $existed_user = DB::table('twitter_users')->where('token', $auth_user->token)->first();

            if (null !== $existed_user) {
                if (Auth::id() === $existed_user->user_id) {
                    $request->session()->put('twitter_id', $existed_user->id);
                }
                return redirect('/twitter');
            }

            $twitter_user = [
                'user_id' => Auth::id(),
                'token' => $auth_user->token,
                'token_secret' => $auth_user->tokenSecret,
            ];
            $new_twitter_user = TwitterUser::create($twitter_user);
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
        if(! $twitter_user) {return 404;}
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
        if(! $twitter_user) {return 404;}
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



<?php

namespace App\Http\Controllers;

use App\User;
use App\TwitterUser;
use function dd;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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

        if (null !== $existed_user){
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

    public function getId(){
        return response(session()->get('twitter_id') ?? '', 200);
    }

    public function setId(){
        $twitter_id = Auth::id();
        session()->put('twitter_id', $twitter_id);
        return Response(200);
    }

    public function logout()
    {
        return session()->forget('twitter_id');
    }

}



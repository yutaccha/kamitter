<?php

namespace App\Http\Controllers;

use App\User;
use App\TwitterUser;
use function dd;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;


class TwitterAuthController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/twitter/provide';

    /**
     * ユーザーをTwitterの認証ページにリダイレクトする
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $auth_user = Socialite::driver('twitter')->user();

            $a = DB::table('twitter_users')->where('token', $auth_user->token)->first();
            if (null !== $a){
                dd($a);
            }

            $twitter_user = [
                'user_id' => 1,
                'token' => $auth_user->token,
                'token_secret' => $auth_user->tokenSecret,
            ];
            TwitterUser::create($twitter_user);
        } catch (Exception $e) {
            return redirect('/');
        }


        return redirect('/');
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('/');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}



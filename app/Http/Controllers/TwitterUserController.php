<?php

namespace App\Http\Controllers;

use App\Http\Components\TwitterApi;
use App\TwitterUser;
use Illuminate\Support\Facades\Auth;

class TwitterUserController extends Controller
{

//    public function __construct()
//    {
//        // 認証が必要
//        //indexは認証しなくても見れるようにする
//        $this->middleware('auth');
//    }

    public function list()
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('user_id', $user_id);
        $my_twitter_accounts = $twitter_user->get();
        $account_num = $twitter_user->count();
        return response([
            'twitter_accounts' => $my_twitter_accounts,
            'account_num' => $account_num], 200);
    }


    public function info(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
        if ($user_id !== $twitter_user->user_id){
            abort(404);
        }
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;

        $json = TwitterApi::useTwitterApi('get', 'account/verify_credentials', 0, $token, $token_secret);
        $twitter_users_data = [
            'name' => $json->name,
            'screen_name' => $json->screen_name,
            'thumbnail' => $json->profile_image_url,
        ];

        return $twitter_users_data;
    }


    public function test()
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::select('token', 'token_secret')->where('user_id', $user_id)->get();
        $twitter_users_data = [];
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $json = TwitterApi::useTwitterApi('get', 'account/verify_credentials', 0, $token, $token_secret);
        $twitter_users_data[] = [
            'name' => $json->name,
            'screen_name' => $json->screen_name,
            'thumbnail' => $json->profile_image_url,
        ];

        return $twitter_users_data;
    }
}

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
//        $this->middleware('auth');
//    }

    public function list()
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('user_id', $user_id);
        $my_twitter_accounts = $twitter_user->get();
        $account_num = $twitter_user->count();
        return ['twitter_accounts' => $my_twitter_accounts, 'account_num' => $account_num];
    }


    public function info(int $id)
    {
        $user_id = Auth::id();
        if (is_null($user_id)){
            abort(419);
        }
        $twitter_user = TwitterUser::where('id', $id)->first();
        if (is_null($twitter_user)){
            abort(404);
        }
        if ($user_id !== $twitter_user->user_id){
            abort(403);
        }
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;

        $json = TwitterApi::useTwitterApi('GET', 'account/verify_credentials', [], $token, $token_secret);
//        dd($json);
        $twitter_users_data = [
            'name' => $json->name,
            'screen_name' => $json->screen_name,
            'thumbnail' =>  str_replace('_normal', '', $json->profile_image_url),
            'follows' => $json->friends_count,
            'followers' => $json->followers_count,
        ];

        return $twitter_users_data;
    }
}

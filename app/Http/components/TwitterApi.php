<?php

namespace App\Http\Components;

use App\User;
use App\TwitterUser;
use Illuminate\Support\Facades\Auth;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterApi
{
    public static function useTwitterApi(String $method = "", $url = 0, $options = 0, $token, $token_secret){
        $api_key = config('services.twitter')['client_id'];
        $api_secret = config('services.twitter')['client_secret'];



        $access_token = $token;
        $access_token_secret = $token_secret;


        $twitter_user = new TwitterOAuth(
            $api_key,
            $api_secret,
            $access_token,
            $access_token_secret
        );

        # 本来はアカウント有効状態を確認するためのものですが、プロフィール取得にも使用可能
        $twitter_user_info = $twitter_user->get($url);
        return $twitter_user_info;

    }
}
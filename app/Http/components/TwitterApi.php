<?php

namespace App\Http\Components;

use App\User;
use App\TwitterUser;
use Illuminate\Support\Facades\Auth;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterApi
{
    public static function useTwitterApi(String $method = "GET", $url = "", $options = [], $token, $token_secret){
        $api_key = config('services.twitter')['client_id'];
        $api_secret = config('services.twitter')['client_secret'];

        $access_token = $token;
        $access_token_secret = $token_secret;

        $twitter_api_connection = new TwitterOAuth(
            $api_key,
            $api_secret,
            $access_token,
            $access_token_secret
        );

        if($method === 'POST'){
            $twitter_api_result = $twitter_api_connection->post($url, $options);
            return $twitter_api_result;
        }else if($method === 'GET'){
            $twitter_api_result = $twitter_api_connection->get($url, $options);
            return $twitter_api_result;
        }

        return [];
    }
}
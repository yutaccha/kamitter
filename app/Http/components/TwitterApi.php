<?php

namespace App\Http\Components;

use App\User;
use App\TwitterUser;
use Illuminate\Support\Facades\Auth;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\SystemManager;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuspendedTwitterAccount;
use App\Mail\ExceededLimit;

class TwitterApi
{
    //APIエラーコード
    const ERROR_CODE_SUSPENDED = 63;
    const ERROR_CODE_LIMIT_EXCEEDED = 88;

    const API_URL_CREDENTIAL = 'account/verify_credentials';

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
        $twitter_api_connection->setTimeouts(3, 15);
        if($method === 'POST'){
            $twitter_api_result = $twitter_api_connection->post($url, $options);
            return $twitter_api_result;
        }
        if($method === 'GET'){
            $twitter_api_result = $twitter_api_connection->get($url, $options);
            return $twitter_api_result;
        }

        return [];
    }

    public static function handleApiError($api_result, $system_manager_id, $twitter_user_id)
    {
        if (property_exists($api_result, 'errors')) {
            foreach ($api_result->errors as $error) {
                //アカウント凍結時の処理
                if ($error->code === self::ERROR_CODE_SUSPENDED) {
                    SystemManager::stopAllServices($system_manager_id);
                    self::sendMail($system_manager_id, $twitter_user_id, self::ERROR_CODE_SUSPENDED);
                    return true;
                }
                //レート制限時の処理
                if ($error->code === self::ERROR_CODE_LIMIT_EXCEEDED) {
                    self::sendMail($system_manager_id, $twitter_user_id, self::ERROR_CODE_LIMIT_EXCEEDED);
                    return true;
                }
            }

        }
        return false;
    }

    public static function sendMail($system_manager_id, $twitter_user_id, $mail_type = 0)
    {
        $system_manager = SystemManager::find($system_manager_id)->with('user')->first();
        $twitter_user = TwitterUser::find($twitter_user_id)->first();
        $user = $system_manager->user;

        if($mail_type === self::ERROR_CODE_SUSPENDED){
            Mail::to($user)->send(new SuspendedTwitterAccount($user, $twitter_user));
        }else if($mail_type === self::ERROR_CODE_LIMIT_EXCEEDED) {
            Mail::to($user)->send(new ExceededLimit($user, $twitter_user));
        }
    }

    public static function fetchTwitterUserInfo($twitter_user)
    {
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_CREDENTIAL,
            $param, $token, $token_secret);

        return $response_json;
    }


}
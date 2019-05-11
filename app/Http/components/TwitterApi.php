<?php

namespace App\Http\Components;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Mail\ExceededLimit;
use App\Mail\SuspendedTwitterAccount;
use App\SystemManager;
use App\TwitterUser;
use Illuminate\Support\Facades\Mail;

/**
 * TwitterAPIを実行するクラス
 * TwitterAPIがエラーになった場合のハンドリングも行う
 * Class TwitterApi
 * @package App\Http\Components
 */
class TwitterApi
{
    //APIエラーコード
    const ERROR_CODE_NOTFOUND = 34;
    const ERROR_CODE_NOUSER = 50;
    const ERROR_CODE_SUSPENDED = 63;
    const ERROR_CODE_LIMIT_EXCEEDED = 88;

    const API_URL_CREDENTIAL = 'account/verify_credentials';
    const API_URL_USERS_SHOW = 'users/show';

    /**
     * TwitterAPIを実行して、結果のJSONを返す
     * @param String $method
     * @param string $url
     * @param array $options
     * @param $token
     * @param $token_secret
     * @return array|object
     */
    public static function useTwitterApi(String $method = "GET", $url = "", $options = [], $token, $token_secret)
    {
        //アクセスキーとアプリケーションキーの取得
        $api_key = config('services.twitter')['client_id'];
        $api_secret = config('services.twitter')['client_secret'];
        $access_token = $token;
        $access_token_secret = $token_secret;

        //接続に必要な接続インスタンスを生成
        $twitter_api_connection = new TwitterOAuth(
            $api_key,
            $api_secret,
            $access_token,
            $access_token_secret
        );
        $twitter_api_connection->setTimeouts(3, 15);
        //POST API実行
        if ($method === 'POST') {
            $twitter_api_result = $twitter_api_connection->post($url, $options);
            return $twitter_api_result;
        }
        //GET API実行
        if ($method === 'GET') {
            $twitter_api_result = $twitter_api_connection->get($url, $options);
            return $twitter_api_result;
        }

        return [];
    }


    /**
     * APIエラーが発生した場合、エラーメッセージに応じてエラー処理を行う
     * 指定のエラーが発生した場合はtrueを返す、それ以外のエラーはfalseを返す
     * @param $api_result
     * @param $system_manager_id
     * @param $twitter_user_id
     * @return bool
     */
    public static function handleApiError($api_result, $system_manager_id, $twitter_user_id)
    {
        if (property_exists($api_result, 'errors')) {
            foreach ($api_result->errors as $error) {
                //アカウント凍結時の処理
                if ($error->code === self::ERROR_CODE_SUSPENDED) {
                    //すべてのサービスを停止
                    SystemManager::stopAllServices($system_manager_id);
                    //メールで凍結を通知
                    self::sendMail($system_manager_id, $twitter_user_id, self::ERROR_CODE_SUSPENDED);
                    return true;
                }
                //レート制限時の処理
                if ($error->code === self::ERROR_CODE_LIMIT_EXCEEDED) {
                    //メールでレート制限を通知
                    self::sendMail($system_manager_id, $twitter_user_id, self::ERROR_CODE_LIMIT_EXCEEDED);
                    return true;
                }
                //ページがない場合の処理
                if ($error->code === self::ERROR_CODE_NOTFOUND) {
                    return true;
                }
                //ユーザーがいない場合の処理
                if ($error->code === self::ERROR_CODE_NOUSER) {
                    return true;
                }

            }
            return false;
        }
    }


    /**
     * APIエラーによるメール送信を行う
     * @param $system_manager_id
     * @param $twitter_user_id
     * @param int $mail_type
     */
    public static function sendMail($system_manager_id, $twitter_user_id, $mail_type = 0)
    {
        $system_manager = SystemManager::find($system_manager_id)->with('user')->first();
        $twitter_user = TwitterUser::find($twitter_user_id)->first();
        $user = $system_manager->user[0];

        if ($mail_type === self::ERROR_CODE_SUSPENDED) {
            //凍結の場合のメール送信
            Mail::to($user)->send(new SuspendedTwitterAccount($user, $twitter_user));
        } else if ($mail_type === self::ERROR_CODE_LIMIT_EXCEEDED) {
            //API制限の場合のメール送信
            Mail::to($user)->send(new ExceededLimit($user, $twitter_user));
        }
    }


    /**
     * バッチで利用するツイッターユーザー情報を取得するAPIを実行する
     * API結果のjsonを返す
     * @param $twitter_user
     * @return array|object
     */
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


    /**
     * ツイッターユーザー情報を取得するAPIを実行する
     * API結果のjsonを返す
     * @param $twitter_user
     * @param $screen
     * @return array|object
     */
    public static function getUsersShow($twitter_user, $screen)
    {
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'screen_name' => $screen,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_USERS_SHOW,
            $param, $token, $token_secret);

        return $response_json;
    }

}
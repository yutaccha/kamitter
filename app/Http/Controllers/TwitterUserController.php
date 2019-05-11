<?php

namespace App\Http\Controllers;

use App\Http\Components\TwitterApi;
use App\TwitterUser;
use Illuminate\Support\Facades\Auth;

/**
 * TwitterUserの情報取得を行う
 * Class TwitterUserController
 * @package App\Http\Controllers
 */
class TwitterUserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * TwitterUserのリストと取得したTwitterUserの総数を返す
     * @return array
     */
    public function list()
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('user_id', $user_id);
        $my_twitter_accounts = $twitter_user->get();
        $account_num = $twitter_user->count();
        return ['twitter_accounts' => $my_twitter_accounts, 'account_num' => $account_num];
    }


    /**
     * APIでTwitterのユーザ情報を取得する
     * @param int $id
     * @return array
     */
    public function info(int $id)
    {

        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
        //存在しないユーザーを取得
        if (is_null($twitter_user)){
            abort(404);
        }
        //他のユーザーのTwitterIdを取得した場合アクセス禁止
        info('user_id', [$user_id]);
        info('$twitter_userid', [$twitter_user]);
        if ($user_id !== $twitter_user->user_id){
            abort(403);
        }

        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;


        try {
            //TwitterApiの実行
            $json = TwitterApi::useTwitterApi('GET', 'users/show', [
                'screen_name' => $twitter_user->screen,
            ], $token, $token_secret);


            $twitter_users_data = [
                'name' => $json->name,
                'screen_name' => $json->screen_name,
                'thumbnail' => str_replace('_normal', '', $json->profile_image_url),
                'follows' => $json->friends_count,
                'followers' => $json->followers_count,
            ];

            return $twitter_users_data;
        }catch (\Exception $e){
            abort(500);
    }


    }
}

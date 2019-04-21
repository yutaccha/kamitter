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
            'account_num' => $account_num
        ], 200);
    }


    public function info(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
//        dd($twitter_user);
//        if ($twitter_user === null){
//            abort(404);
//        }
        if ($user_id !== $twitter_user->user_id){
            abort(403);
        }
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;

        $json = TwitterApi::useTwitterApi('get', 'account/verify_credentials', [], $token, $token_secret);
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

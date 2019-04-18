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

class TwitterUserController extends Controller
{
    public function list(){
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('user_id', $user_id);
        $my_twitter_accounts = $twitter_user->get();
        $account_num = $twitter_user->count();
        return response([
            'twitter_accounts' => $my_twitter_accounts,
            'account_num' => $account_num], 200);
    }
}

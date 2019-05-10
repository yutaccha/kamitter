<?php

namespace App\Http\Controllers;

use App\SystemManager;
use App\TwitterUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

/**
 * TwitterUserの新規追加、削除を行う
 * Twitterアプリケーション認証を行う
 * TwitterUserのセッション管理を行う
 *
 * Class TwitterAuthController
 * @package App\Http\Controllers
 */
class TwitterAuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('auth')->except(['getId', 'handleProviderCallback']);
    }

    /**
     * ユーザーをTwitterの認証ページにリダイレクトする
     * @return Response
     */
    public function oauth()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Twitter認証ページからのリダイレクトを受け取る
     * レスポンスデータを元にTwitterUserの新規追加処理を行う
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(Request $request)
    {
        //認証キャンセルの場合の処理
        if ($request->query('denied')) {
            return redirect('/twitter');
        }
        //必要なパラメータが存在しない場合の処理
        if (is_null($request->query('oauth_token')) || is_null('oauth_verifier')) {
            abort('404');
        }

        $user_id = Auth::id();
        try {
            //ユーザーデータとアクセストークンの取得
            $auth_twitter_user = Socialite::driver('twitter')->user();
            //すでに登録されたデータが有れば取得
            $exist_twitter_user = DB::table('twitter_users')->where('token', $auth_twitter_user->token)->first();

        } catch (\Exception $e) {
            return abort(500);
        }

        //登録データが有る場合、sessionに格納してリダイレクト
        if (!is_null($exist_twitter_user)) {
            if ($user_id === $exist_twitter_user->user_id) {
                $request->session()->put('twitter_id', $exist_twitter_user->id);
            }
            return redirect('/twitter');
        }

        //新登録の場合TwitterUserデータをDBに保存してリダイレクト
        $twitter_user = [
            'user_id' => $user_id,
            'token' => $auth_twitter_user->token,
            'token_secret' => $auth_twitter_user->tokenSecret,
            'screen' => $auth_twitter_user->nickname,
        ];
        $new_twitter_user = TwitterUser::create($twitter_user);

        $system_manager = new SystemManager();
        $system_manager->user_id = $user_id;
        $system_manager->twitter_user_id = $new_twitter_user->id;
        $system_manager->save();

        $request->session()->put('twitter_id', $new_twitter_user->id);
        return redirect('/twitter');
    }

    /**
     * セッションに登録された現在操作中のTwitterUserIdを取得する
     * @return mixed|string twitter_user_id
     */
    public function getId()
    {
        return session()->get('twitter_id') ?? '';
    }


    /**
     * 現在操作中のTwitterUserIdをセッションに格納する
     * @param int $id
     */
    public function setId(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
        if (is_null($twitter_user)) {
            abort(404);
        }
        //他人のデータを変更させない
        if ($twitter_user->user_id !== $user_id) {
            abort(403);
        }
        session()->put('twitter_id', $id);
    }

    /**
     * TwitterUserを削除する
     * @param int $id
     */
    public function delete(int $id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $id)->first();
        if (is_null($twitter_user)) {
            abort(404);
        }
        //他人のデータを変更させない
        if ($twitter_user->user_id !== $user_id) {
            abort(403);
        }
        $twitter_user->delete();
    }

    /**
     * 現在操作中のTwitterUserIdをセッションから削除する
     */
    public function logout()
    {
        session()->forget('twitter_id');
    }
}



<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticLike;
use App\Http\Requests\AddAutomaticLike;
use App\Http\Controllers\TwitterAuthController;
use Illuminate\Support\Facades\Auth;
use App\TwitterUser;


/**
 * 自動いいね設定に関する操作を行う
 * Class AutomaticLikeController
 * @package App\Http\Controllers
 */
class AutomaticLikeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 新規自動いいね設定を追加する
     * @param AddAutomaticLike $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add(AddAutomaticLike $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_like = new AutomaticLike();
        $auto_like->twitter_user_id = $twitter_user_id;
        $auto_like->filter_word_id = $request->filter_word_id;

        Auth::user()->automaticLikes()->save($auto_like);
        $new_auto_like = AutomaticLike::where('id', $auto_like->id)->with('filterWord')->first();
        return response($new_auto_like, 201);
    }


    /**
     * 登録した自動いいね設定一覧を取得する
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show()
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_likes = AutomaticLike::where('twitter_user_id', $twitter_user_id)->with('filterWord')->get();
        return response($auto_likes, 200);
    }


    /**
     * 自動いいね設定情報を変更する
     * @param int $id
     * @param AddAutomaticLike $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit(int $id, AddAutomaticLike $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_like = AutomaticLike::where('id', $id)->with('filterWord')->first();
        if (! $auto_like){
            abort(404);
        }
        $auto_like->filter_word_id = $request->filter_word_id;
        $auto_like->save();

        return response($auto_like, 200);
    }


    /**
     * 自動いいね情報を変更する
     * @param int $id
     */
    public function delete(int $id)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_like = AutomaticLike::where('id', $id)->first();
        if (! $auto_like){
            abort(404);
        }
        $auto_like->delete();
    }


    /**
     * TwitterUserが見つからなかった場合にはNOTFOUND
     * 権限のないアクセスをした場合にはForbiddenを返す
     * @param $twitter_user_id
     */
    public function authCheck($twitter_user_id)
    {
        $user_id = Auth::id();
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        if (is_null($twitter_user)) {
            abort(404);
        }
        if ($twitter_user->user_id !== $user_id) {
            abort(403);
        }
    }
}

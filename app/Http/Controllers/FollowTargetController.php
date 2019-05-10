<?php

namespace App\Http\Controllers;

use App\FollowerTarget;
use App\FollowTarget;
use App\Http\Requests\AddFollowTarget;
use Illuminate\Support\Facades\Auth;
use App\TwitterUser;

/**
 * 自動フォロー設定に関する操作を行う
 * Class FollowTargetController
 * @package App\Http\Controllers
 */
class FollowTargetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 新規のフォローターゲットを追加する
     * @param AddFollowTarget $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add(AddFollowTarget $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $follow_target = new FollowTarget();
        $follow_target->twitter_user_id = $twitter_user_id;
        $follow_target->filter_word_id = $request->filter_word_id;
        $follow_target->target = $request->target;

        Auth::user()->followTargets()->save($follow_target);
        $new_auto_like = FollowTarget::where('id', $follow_target->id)->with('filterWord')->first();
        return response($new_auto_like, 201);
    }


    /**
     * 登録したフォローターゲット一覧を取得する
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show()
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $follow_target = FollowTarget::where('twitter_user_id', $twitter_user_id)
            ->whereIn('status', [1, 2, 3])->orderby('created_at', 'desc')->limit(30)
            ->with('filterWord')->get();

        return response($follow_target, 200);
    }


    /**
     * フォローターゲット情報を修正する
     * @param int $id
     * @param AddFollowTarget $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit(int $id, AddFollowTarget $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $follow_target = FollowTarget::where('id', $id)->with('filterWord')->first();
        if (!$follow_target) {
            abort(404);
        }
        $follow_target->filter_word_id = $request->filter_word_id;
        $follow_target->target = $request->target;
        $follow_target->save();

        return response($follow_target, 200);
    }

    /**
     * フォローターゲットを削除する
     * @param int $id
     */
    public function delete(int $id)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $twitter_user_id = session()->get('twitter_id');
        $follow_target = FollowTarget::where('id', $id)->first();
        $status_under_making_list = 3;

        if (!$follow_target) {
            abort(404);
        }
        if ($follow_target->status === $status_under_making_list) {
            FollowerTarget::where('twitter_user_id', $twitter_user_id)->delete();
        }
        $follow_target->delete();
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

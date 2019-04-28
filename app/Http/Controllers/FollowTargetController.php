<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddFollowTarget;
use App\FollowTarget;
use App\Http\Requests\AddAutomaticLike;
use App\Http\Controllers\TwitterAuthController;
use App\FollowerTarget;
use Illuminate\Support\Facades\Auth;

class FollowTargetController extends Controller
{
    public function add(AddFollowTarget $request)
    {
        $twitter_id = session()->get('twitter_id');

        $follow_target = new FollowTarget();
        $follow_target->twitter_user_id = $twitter_id;
        $follow_target->filter_word_id = $request->filter_word_id;
        $follow_target->target = $request->target;

        Auth::user()->followTargets()->save($follow_target);
        $new_auto_like = FollowTarget::where('id', $follow_target->id)->with('filterWord')->first();
        return response($new_auto_like, 201);
    }

    public function show()
    {
        $twitter_id = session()->get('twitter_id');
        $follow_target = FollowTarget::where('twitter_user_id', $twitter_id)->with('filterWord')->get();
        return response($follow_target, 200);
    }

    public function edit(int $id, AddFollowTarget $request)
    {
        $follow_target = FollowTarget::where('id', $id)->with('filterWord')->first();
        if (! $follow_target){
            abort(404);
        }
        $follow_target->filter_word_id = $request->filter_word_id;
        $follow_target->target = $request->target;
        $follow_target->save();

        return response($follow_target, 200);
    }

    public function delete(int $id)
    {
        $twitter_id = session()->get('twitter_id');
        $follow_target = FollowTarget::where('id', $id)->first();
        $status_under_making_list = 3;

        if (! $follow_target){
            abort(404);
        }
        if($follow_target->status === $status_under_making_list ){
            FollowerTarget::where('twitter_user_id', $twitter_id)->delete();
        }
        $follow_target->delete();
    }

}

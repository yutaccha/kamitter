<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticLike;
use App\Http\Requests\AddAutomaticLike;
use App\Http\Controllers\TwitterAuthController;
use Illuminate\Support\Facades\Auth;

class AutomaticLikeController extends Controller
{
    public function add(AddAutomaticLike $request)
    {
        $user_id = Auth::id();
        $twitter_id = session()->get('twitter_id');

        $auto_like = new AutomaticLike();
        $auto_like->twitter_user_id = $twitter_id;
        $auto_like->filter_word_id = $request->filter_word_id;

        Auth::user()->automaticLikes()->save($auto_like);
        $new_auto_like = AutomaticLike::where('id', $auto_like->id)->with('filterWord')->first();
        return response($new_auto_like, 201);
    }

    public function show()
    {
        $twitter_id = session()->get('twitter_id');
        $auto_likes = AutomaticLike::where('twitter_user_id', $twitter_id)->with('filterWord')->get();
        return response($auto_likes);
    }

    public function edit(int $id, AddAutomaticLike $request)
    {
        $auto_like = AutomaticLike::where('id', $id)->first();
        if (! $auto_like){
            abort(404);
        }
        $auto_like->filter_word_id = $request->filter_word_id;
        $auto_like->save();

        return response($auto_like, 200);
    }

    public function delete(int $id)
    {
        $auto_like = AutomaticLike::where('id', $id)->first();
        if (! $auto_like){
            abort(404);
        }
        $auto_like->delete();
    }
}

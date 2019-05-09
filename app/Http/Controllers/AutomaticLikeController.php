<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticLike;
use App\Http\Requests\AddAutomaticLike;
use App\Http\Controllers\TwitterAuthController;
use Illuminate\Support\Facades\Auth;
use App\TwitterUser;

class AutomaticLikeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

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

    public function show()
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_likes = AutomaticLike::where('twitter_user_id', $twitter_user_id)->with('filterWord')->get();
        return response($auto_likes, 200);
    }

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

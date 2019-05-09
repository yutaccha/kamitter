<?php

namespace App\Http\Controllers;

use App\AutomaticTweet;
use App\Http\Requests\AddAutomaticTweet;
use App\TwitterUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AutomaticTweetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add(AddAutomaticTweet $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_tweet = new AutomaticTweet();
        $auto_tweet->twitter_user_id = $twitter_user_id;
        $auto_tweet->tweet = $request->tweet;
        $auto_tweet->submit_date = $request->date_time;

        Auth::user()->automaticTweets()->save($auto_tweet);
        $new_auto_tweet = AutomaticTweet::where('id', $auto_tweet->id)->with('user')->first();
        return response($new_auto_tweet, 201);
    }

    public function show()
    {
        $twitter_user_id = session()->get('twitter_id');
        $before_7days = Carbon::now()->addDay(-7);
        $auto_tweets = AutomaticTweet::where('twitter_user_id', $twitter_user_id)
            ->whereDate('submit_date', '>', $before_7days)->orderBy('submit_date')->get();

        return response($auto_tweets, 200);
    }

    public function edit(int $id, AddAutomaticTweet $request)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);
        $auto_tweet = AutomaticTweet::where('id', $id)->first();
        if (!$auto_tweet) {
            abort(404);
        }
        $auto_tweet->tweet = $request->tweet;
        $auto_tweet->submit_date = $request->date_time;
        $auto_tweet->save();

        return response($auto_tweet, 200);
    }

    public function delete(int $id)
    {
        $twitter_user_id = session()->get('twitter_id');
        $this->authCheck($twitter_user_id);

        $auto_tweet = AutomaticTweet::where('id', $id)->first();
        if (!$auto_tweet) {
            abort(404);
        }
        $auto_tweet->delete();
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

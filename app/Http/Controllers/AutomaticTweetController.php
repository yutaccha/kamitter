<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticTweet;
use App\Http\Requests\AddAutomaticTweet;
use App\Http\Controllers\TwitterAuthController;
use Illuminate\Support\Facades\Auth;

class AutomaticTweetController extends Controller
{
    public function add(AddAutomaticTweet $request)
    {
        $twitter_id = session()->get('twitter_id');

        $auto_tweet = new AutomaticTweet();
        $auto_tweet->twitter_user_id = $twitter_id;
        $auto_tweet->tweet = $request->tweet;
        $auto_tweet->submit_date = $request->date_time;

        Auth::user()->automaticTweets()->save($auto_tweet);
        $new_auto_tweet = AutomaticTweet::where('id', $auto_tweet->id)->with('user')->first();
        return response($new_auto_tweet, 201);
    }

    public function show()
    {
        $twitter_id = session()->get('twitter_id');
        $auto_tweets = AutomaticTweet::where('twitter_user_id', $twitter_id)->get();
        return response($auto_tweets, 200);
    }

    public function edit(int $id, AddAutomaticTweet $request)
    {
        $auto_tweet = AutomaticTweet::where('id', $id)->first();
        if (! $auto_tweet){
            abort(404);
        }
        $auto_tweet->tweet = $request->tweet;
        $auto_tweet->submit_date = $request->date_time;
        $auto_tweet->save();

        return response($auto_tweet, 200);
    }

    public function delete(int $id){
        $auto_tweet = AutomaticTweet::where('id', $id)->first();
        if (! $auto_tweet){
            abort(404);
        }
        $auto_tweet->delete();
    }
}

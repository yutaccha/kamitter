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
        $auto_tweet->twitter_user_id = 1;
        $auto_tweet->status = $request->status;
        $auto_tweet->tweet = $request->tweet;
        $auto_tweet->submit_date = $request->date_time;

//        dd($auto_tweet);

        Auth::user()->automaticTweets()->save($auto_tweet);
        $new_auto_tweet = AutomaticTweet::where('id', $auto_tweet->id)->with('user')->first();
        return response($new_auto_tweet, 201);
    }
}

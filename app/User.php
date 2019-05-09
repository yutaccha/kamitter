<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'name', 'email'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * リレーションシップ　- twitter_usersテーブル
     */
    public function twitterUsers()
    {
        return $this->hasMany('App\TwitterUser', 'user_id');
    }

    /**
     * リレーションシップ　- filter_wordsテーブル
     */
    public function filterWords()
    {
        return $this->hasMany('App\FilterWord', 'user_id');
    }

    /**
     * リレーションシップ　- automatic_tweetsテーブル
     */
    public function automaticTweets()
    {
        return $this->hasMany('App\AutomaticTweet', 'user_id');
    }

    /**
     * リレーションシップ　- automatic_likesテーブル
     */
    public function automaticLikes()
    {
        return $this->hasMany('App\AutomaticLike', 'user_id');
    }

    /**
     * リレーションシップ　- follow_targetsテーブル
     */
    public function followTargets()
    {
        return $this->hasMany('App\FollowTarget', 'user_id');

    }

    /**
     * リレーションシップ　- system_managersテーブル
     */
    public function systemManagers()
    {
        return $this->hasMany('App\SystemManages', 'user_id');

    }

}

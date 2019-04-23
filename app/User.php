<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'id', 'name'
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
     * リレーションシップ　- TwitterUsersテーブル
     */
    public function twitterUsers()
    {
        return $this->hasMany('App\TwitterUser', 'user_id');
    }

    /**
     * リレーションシップ　- FilterWordsテーブル
     */
    public function filterWords()
    {
        return $this->hasMany('App\FilterWord', 'user_id');
    }

    /**
     * リレーションシップ　- AutomaticTweetsテーブル
     */
    public function automaticTweets()
    {
        return $this->hasMany('App\AutomaticTweet', 'user_id');
    }

    public function automaticLikes()
    {
        return $this->hasMany('App\AutomaticLike', 'user_id');
    }

    public function followTargets()
    {
        return $this->hasMany('App\FollowTarget', 'user_id');

    }

}

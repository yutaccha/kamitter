<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterUser extends Model
{
    protected $table = 'twitter_users';

    protected $fillable = [
        'user_id', 'token', 'token_secret'
    ];

    protected $visible = [
        'id'
    ];

    /**
     * リレーションシップ -usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
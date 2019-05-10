<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * 自動いいねに使用するモデル
 * Class AutomaticLike
 * @package App
 */
class AutomaticLike extends Model
{
    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'twitter_user_id'
    ];

    /**
     * リレーションシップ　- usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * リレーションシップ　- twitter_usersテーブル
     */
    public function twitterUser()
    {
        return $this->belongsTo('App\TwitterUser', 'twitter_user_id');
    }

    /**
     * リレーションシップ　- filter_wordsテーブル
     */
    public function filterWord()
    {
        return $this->belongsTo('App\FilterWord', 'filter_word_id');
    }

}

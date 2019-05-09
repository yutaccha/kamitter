<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowTarget extends Model
{
    const STATUS = [
        1 => ['label' => '待機中'],
        2 => ['label' => 'リスト作成中'],
        3 => ['label' => 'リスト作成済']
    ];

    protected $appends = [
        'status_label'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];


    /**
     * リレーションシップ - usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }


    /**
     * リレーションシップ - twitter_usersテーブル
     */
    public function twitterUser()
    {
        return $this->belongsTo('App\TwitterUser', 'twitter_user_id');
    }


    /**
     * リレーションシップ - filter_wordsテーブル
     */
    public function filterWord()
    {
        return $this->belongsTo('App\FilterWord', 'filter_word_id');
    }


    /**
     * アクセサ - status_label
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $status = $this->attributes['status'];

        if (!isset(self::STATUS[$status])) {
            return '';
        }

        return self::STATUS[$status]['label'];
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 自動ツイートに使用するモデル
 * Class AutomaticTweet
 * @package App
 */
class AutomaticTweet extends Model
{
    const STATUS = [
        1 => ['label' => '未送信'],
        2 => ['label' => 'ツイート済'],
    ];


    protected $appends = [
        'status_label', 'formatted_date', 'japanese_formatted_date'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'submit_date'
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


    /**
     * アクセサ - formatted_date
     * @return string
     * @throws \Exception
     */
    public function getFormattedDateAttribute()
    {
        $submit_date = $this->attributes['submit_date'];
        $date = new \DateTime($submit_date);
        return $date->format('Y-m-d H:i');
    }

    /**
     * アクセサ - japanese_formatted_date
     * @return string
     * @throws \Exception
     */
    public function getJapaneseFormattedDateAttribute()
    {
        $submit_date = $this->attributes['submit_date'];
        $date = new \DateTime($submit_date);
        return $date->format('Y年m月d日 H時i分');
    }
}


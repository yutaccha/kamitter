<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomaticTweet extends Model
{
    const STATUS = [
        1 => ['label' => '未送信'],
        2 => ['label' => 'ツイート済'],
    ];

    protected $appends = [
      'status_label', 'formatted_date'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'submit_date'
    ];

    public function user()
    {
        return $this->belongsTo('App\User' ,'user_id');
    }

    public function twitterUser()
    {
        return $this->belongsTo('App\TwitterUser', 'twitter_user_id');
    }

    public function getStatusLabelAttribute()
    {
        $status = $this->attributes['status'];

        if(!isset(self::STATUS[$status])){
            return '';
        }

        return self::STATUS[$status]['label'];
    }

    public function getFormattedDateAttribute()
    {
        $submit_date = $this->attributes['submit_date'];
        $date = new \DateTime($submit_date);
        return $date->format('Y-m-d H:i');
    }
}


<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomaticTweet extends Model
{
    const STATUS = [
        1 => ['label' => '未送信'],
        2 => ['label' => '送信済'],
    ];

    protected $appends = [
      'status_label',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
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
}

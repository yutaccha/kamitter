<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use  App\Http\Requests\EditSystemManager;

class SystemManager extends Model
{
    protected $appends = [
        'status_labels'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    const TYPE = [
        1 => 'auto_follow',
        2 => 'auto_unfollow',
        3 => 'auto_like',
        4 => 'auto_tweet',
    ];

    const STATUS = [
        1 => ['label' => 'サービス停止'],
        2 => ['label' => 'サービス稼動中'],
        3 => ['label' => 'API制限中'],
    ];

    /**
     * リレーションシップ -usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * リレーションシップ -twitter_usersテーブル
     */
    public function twitterUser()
    {
        return $this->belongsTo('App\TwitterUser', 'user_id');
    }


    public function getStatusLabelsAttribute()
    {
        $status_labels = [];
        foreach (self::TYPE as $key => $service_name){
            $status = $this->attributes[$service_name.'_status'];
            $label = self::STATUS[$status]['label'];
            $status_labels[$service_name] = $label;
        }

        return $status_labels;
    }

    public static function stopAllServices($id)
    {
        $system_manager = self::where('id', $id)->first();
        $system_manager->auto_follow_status = 1;
        $system_manager->auto_unfollow_status = 1;
        $system_manager->auto_like_status = 1;
        $system_manager->auto_tweet_status = 1;
        $system_manager->save();
    }
}

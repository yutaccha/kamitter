<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use  App\Http\Requests\EditSystemManager;

/**
 * 各サービスのサービスステータスを扱うモデル
 * Class SystemManager
 * @package App
 */
class SystemManager extends Model
{
    protected $appends = [
        'status_labels'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
      'auto_follow_status' => 'integer',
        'auto_unfollow_status' => 'integer',
        'auto_like_status' => 'integer',
        'auto_tweet_status' => 'integer'
    ];

    const TYPE = [
        1 => 'auto_follow',
        2 => 'auto_unfollow',
        3 => 'auto_like',
        4 => 'auto_tweet',
    ];

    //サービスステータス
    const STATUS_STOP = 1;
    const STATUS_RUNNING = 2;
    const STATUS_WAIT_API_RESTRICTION = 3;

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


    /**
     * それぞれの自動サービスのステータスラベルを返す
     * アクセサ - status_labels
     * @return array
     */
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


    /**
     * すべてのサービスを停止状態にする
     * @param $id
     */
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

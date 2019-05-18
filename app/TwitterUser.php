<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * アプリに登録したツイッターユーザー情報を扱うモデル
 * Class TwitterUser
 * @package App
 */
class TwitterUser extends Model
{
    protected $table = 'twitter_users';

    protected $fillable = [
        'user_id', 'token', 'token_secret', 'screen'
    ];

    protected $visible = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * リレーションシップ -usersテーブル
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * リレーションシップ　- system_managersテーブル
     */
    public function systemManagers()
    {
        return $this->hasMany('App\SystemManager', 'twitter_user_id');

    }
}
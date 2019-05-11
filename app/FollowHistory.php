<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * フォロー履歴に関するモデル
 * Class FollowHistory
 * @package App
 */
class FollowHistory extends Model
{
    //
    protected $casts = [
        'twitter_user_id' => 'integer',
    ];
}

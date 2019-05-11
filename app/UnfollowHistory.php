<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * アンフォロー履歴に関するモデル
 * Class UnfollowHistory
 * @package App
 */
class UnfollowHistory extends Model
{
    //
    protected $casts = [
        'twitter_user_id' => 'integer',
        'id' => 'integer'
    ];
}

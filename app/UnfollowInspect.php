<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * アクティブユーザー検査に関するモデル
 * Class UnfollowInspect
 * @package App
 */
class UnfollowInspect extends Model
{
    //
    protected $fillable = [
      'twitter_user_id', 'twitter_id',
    ];
}

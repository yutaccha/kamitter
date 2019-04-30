<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowInspect extends Model
{
    //
    protected $fillable = [
      'twitter_user_id', 'twitter_id',
    ];
}

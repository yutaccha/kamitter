<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomaticLike extends Model
{
    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'twitter_user_id'
    ];

    public function filterWord()
    {
        return $this->belongsTo('App\FilterWord', 'filter_word_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}

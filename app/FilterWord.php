<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilterWord extends Model
{
    const TYPE = [
        1 => ['label' => '次のワードを含む'],
        2 => ['label' => 'いずれかのワードを含む'],
    ];

    protected $appends = [
        'type_label', 'merged_word'
    ];

    protected $hidden = [
      'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User' ,'user_id');
    }

    public function automaticLikes()
    {
        return $this->hasMany('App\AutomaticLike', 'filter_word_id');
    }


    public function getTypeLabelAttribute()
    {
        $type = $this->attributes['type'];

        if(!isset(self::TYPE[$type])){
            return '';
        }

        return self::TYPE[$type]['label'];
    }

    public function getMergedWordAttribute()
    {
        $type = $this->attributes['type'];
        if(!isset(self::TYPE[$type])){
            return '';
        }
        $type_string = self::TYPE[$type]['label'];

        $word = $this->attributes['word'];
        $remove = $this->attributes['remove'];

        return "$type_string ： [$word] 、 除外ワード：[$remove]";
    }
}

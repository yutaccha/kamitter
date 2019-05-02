<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilterWord extends Model
{
    const AND = 1;
    const OR = 2;
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

    public function getMergedWordStringForQuery()
    {
        $type = $this->attributes['type'];
        $word = $this->attributes['word'];
        $str_word = ($type === self::OR) ? str_replace(" ", " OR ", $word) : $word;
        $remove = (!empty($remove)) ? $this->generateRemoveString($this->attributes['remove']) : "";

        return $str_word . $remove . ' OR @z_zz__zz1928 -filter:retweets lang:ja';
    }

    private function generateRemoveString($word)
    {
        $exploded_words = explode(" ", $word);
        $remove_string = '';
        foreach ($exploded_words as $exploded_word){
            $remove_string = $remove_string. ' -"'. $exploded_word. '"';
        }
        return $remove_string;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilterWord extends Model
{
    const TYPE = [
        1 => ['label' => '〜を含む'],
        2 => ['label' => '〜のいずれかを含む'],
        3 => ['label' => '〜を除く'],
    ];

    protected $appends = [
        'type_label',
    ];

    protected $hidden = [
      'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User' ,'user_id');
    }

    public function getTypeLabelAttribute()
    {
        $type = $this->attributes['type'];

        if(!isset(self::TYPE[$type])){
            return '';
        }

        return self::TYPE[$type]['label'];
    }
}

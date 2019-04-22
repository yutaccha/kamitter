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

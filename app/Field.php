<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'tag_id',
        'type',
        'name',
        'code',
    ];

    public function tag()
    {
        return $this->belongsTo('App\Tag');
    }
}

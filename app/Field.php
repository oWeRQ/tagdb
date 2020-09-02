<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public function tag()
    {
        return $this->belongsTo('App\Tag');
    }
}

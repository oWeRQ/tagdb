<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function entities()
    {
        return $this->belongsToMany('App\Entity', 'entities_tags');
    }

    public function fields()
    {
        return $this->hasMany('App\Field');
    }
}

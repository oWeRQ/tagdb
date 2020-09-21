<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'query',
    ];

    public function getTagsAttribute()
    {
        return Tag::whereIn('name', json_decode($this->query)->tags)->get();
    }
}

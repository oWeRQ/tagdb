<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Preset extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'query',
    ];

    public function getTagsAttribute()
    {
        return Tag::whereIn(DB::raw('LOWER(name)'), json_decode($this->query)->tags)->get();
    }
}

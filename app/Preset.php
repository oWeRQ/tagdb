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

    public function entityQuery($sort = null)
    {
        $query = Entity::query();
        $query->queryJson($this->query);
        $query->sort($sort ?: $this->sort);
        return $query;
    }

    public function getTagsAttribute()
    {
        return Tag::whereIn('name', json_decode($this->query)->tags)->get();
    }
}

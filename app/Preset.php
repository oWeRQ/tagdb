<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProjectScope;

class Preset extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'query',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array(
            'project_id' => request()->project()->id,
        ), true);
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProjectScope);
    }

    public function scopeSort($query, $sort = null)
    {
        if (!$sort)
            return $query->orderBy('name', 'asc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            $query->orderBy($column[0], $direction);
        }

        return $query;
    }

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

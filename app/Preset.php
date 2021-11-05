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
            'project_id' => auth()->user()->currentProject->id,
        ), true);
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProjectScope);
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProjectScope;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array(
            'project_id' => auth()->user()->currentProjectId,
        ), true);
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProjectScope);
    }

    public static function fromNames($names)
    {
        return collect($names)->map(function($name) {
            return static::firstOrCreate(['name' => $name]);
        });
    }

    public static function parseString($tagsString)
    {
        return preg_split('/\s*,\s*/', trim($tagsString), -1, PREG_SPLIT_NO_EMPTY);
    }

    public function entities()
    {
        return $this->belongsToMany('App\Entity', 'entities_tags');
    }

    public function fields()
    {
        return $this->hasMany('App\Field');
    }

    public function scopeSort($query, $sort = null)
    {
        if (!$sort)
            return $query->orderBy('entities_count', 'desc')->orderBy('name', 'asc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            $query->orderBy($column[0], $direction);
        }

        return $query;
    }

    public function updateFields(array $fields = null)
    {
        if (!is_array($fields))
            return;

        $ids = [];
        foreach ($fields as $field) {
            $ids[] = Field::updateOrCreate([
                'id' => $field['id'] ?? null,
                'tag_id' => $this->id,
            ], $field)->id;
        }

        foreach ($this->fields as $field) {
            if (!in_array($field->id, $ids)) {
                $field->delete();
            }
        }
    }
}

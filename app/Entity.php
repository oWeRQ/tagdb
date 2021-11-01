<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProjectScope;

class Entity extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $visible = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'tags',
        'values',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array(
            'project_id' => Project::getCurrentId(),
        ), true);
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProjectScope);
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'entities_tags');
    }

    public function values()
    {
        return $this->hasMany('App\Value');
    }

    public function valuesAssoc() {
        return $this->values->mapWithKeys(function($value) {
            return [$value->field->code => $value->content];
        })->all();
    }

    public function scopeQueryJson($query, $json)
    {
        $queryData = json_decode($json, true);

        $query->havingTags($queryData['tags'] ?? null);
        $query->search($queryData['search'] ?? null);

        return $query;
    }

    public function scopeHavingTags($query, array $tags = null)
    {
        if (!$tags)
            return $query;

        $includeTags = [];
        $excludeTags = [];
        foreach ($tags as $tag) {
            if ($tag[0] === '-') {
                $excludeTags[] = trim($tag, '+-');
            } else {
                $includeTags[] = trim($tag, '+-');
            }
        }

        if ($includeTags) {
            $query->whereHas('tags', function($query) use($includeTags) {
                $query->whereIn('name', $includeTags);
            }, '=', count($includeTags));
        }

        if ($excludeTags) {
            $query->whereDoesntHave('tags', function($query) use($excludeTags) {
                $query->whereIn('name', $excludeTags);
            });
        }

        return $query;
    }

    public function scopeSearch($query, $search = null)
    {
        if (!$search)
            return $query;

        return $query->where('name', 'like', '%'.$search.'%')->orWhereHas('values', function($query) use($search) {
            $query->where('content', 'like', '%'.$search.'%');
        });
    }

    public function scopeSelectContents($query, $field_id = null)
    {
        $column = "values_$field_id.content as contents.$field_id";

        if (!in_array($column, $query->getQuery()->columns)) {
            $query->leftJoin("values as values_$field_id", function($join) use($field_id) {
                $join->on("values_$field_id.entity_id", '=', 'entities.id');
                $join->where("values_$field_id.field_id", '=', $field_id);
            })->addSelect($column);
        }
    }

    public function scopeSort($query, $sort = null)
    {
        $query->select('entities.*');

        if (!$sort)
            return $query->orderBy('created_at', 'desc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            if ($column[0] === 'contents') {
                $field_id = $column[1];
                $query->selectContents($field_id)->orderBy("values_$field_id.content", $direction);
            } else {
                $query->orderBy($column[0], $direction);
            }
        }

        return $query;
    }

    public function updateTags(array $tags = null)
    {
        if (!is_array($tags))
            return;

        $tagIds = array_map(function($tag) {
            if (empty($tag['id'])) {
                return Tag::firstOrCreate([
                    'name' => $tag['name'],
                ], $tag)->id;
            }

            return $tag['id'];
        }, $tags);

        $this->tags()->sync($tagIds);
    }

    public function updateContents(array $contents = null)
    {
        if (!$contents)
            return;

        foreach ($contents as $field_id => $content) {
            Value::updateOrCreate([
                'entity_id' => $this->id,
                'field_id' => $field_id,
            ], [
                'content' => (string)$content,
            ]);
        }
    }

    public function updateValues($values) {
        $fields = $this->tags()->with('fields')->get()->pluck('fields')->collapse();

        $fieldIds = $fields->pluck('id', 'code')->all();
        $contents = collect($values)->only(array_keys($fieldIds))->keyBy(function($content, $key) use($fieldIds) {
            return $fieldIds[$key];
        })->all();

        return $this->updateContents($contents);
    }
}

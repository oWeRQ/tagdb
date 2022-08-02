<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
            'project_id' => request()->project()->id,
        ), true);
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProjectScope);
    }

    public static function deleteById($id)
    {
        static::find($id)->each(function($entity) {
            $entity->delete();
        });
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

    public function scopeQueryJson(Builder $builder, $json)
    {
        $queryData = json_decode($json, true);

        $builder->havingTags($queryData['tags'] ?? null);
        $builder->filter($queryData['filter'] ?? null);
        $builder->search($queryData['search'] ?? null);
    }

    public function scopeHavingTags(Builder $builder, array $tags = null)
    {
        if (!$tags)
            return $builder;

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
            $builder->whereHas('tags', function($builder) use($includeTags) {
                $builder->whereIn('name', $includeTags);
            }, '=', count($includeTags));
        }

        if ($excludeTags) {
            $builder->whereDoesntHave('tags', function($builder) use($excludeTags) {
                $builder->whereIn('name', $excludeTags);
            });
        }
    }

    protected function escapeLike($value)
    {
        return preg_replace('/[%_]/', '\\\\$0', $value);
    }

    public function scopeFilter(Builder $builder, $filter = null)
    {
        if ($filter) {
            $operatorMap = [
                'eq' => '=',
                'gt' => '>',
                'gte' => '>=',
                'lt' => '<',
                'lte' => '<=',
            ];

            foreach ($filter as $column => $operators) {
                $column = explode('.', $column);
                foreach ($operators as $operator => $value) {
                    if ($column[0] === 'contents') {
                        $field_id = $column[1];
                        $field_column = "values_$field_id.content";
                        $builder->selectContents($field_id);
                    } else {
                        $field_column = $column[0];
                    }

                    if ($operator === 'in') {
                        $builder->whereIn($field_column, $value);
                    } elseif ($operator === 'like') {
                        $builder->where($field_column, 'like', '%'.$this->escapeLike($value).'%');
                    } else {
                        $builder->where($field_column, $operatorMap[$operator], $value);
                    }
                }
            }
        }
    }

    public function scopeSearch(Builder $builder, $search)
    {
        if ($search == '')
            return;

        $builder->where('name', 'like', '%'.$this->escapeLike($search).'%')->orWhereHas('values', function($builder) use($search) {
            $builder->where('content', 'like', '%'.$this->escapeLike($search).'%');
        });
    }

    public function scopeSelectContents(Builder $builder, $field_id = null)
    {
        $column = "values_$field_id.content as contents.$field_id";

        if (!in_array($column, (array)$builder->getQuery()->columns)) {
            $builder->leftJoin("values as values_$field_id", function($join) use($field_id) {
                $join->on("values_$field_id.entity_id", '=', 'entities.id');
                $join->where("values_$field_id.field_id", '=', $field_id);
            })->addSelect($column);
        }
    }

    public function scopeSort(Builder $builder, $sort = null)
    {
        $builder->addSelect('entities.*');

        if (!$sort)
            return $builder->orderBy('created_at', 'desc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            if ($column[0] === 'contents') {
                $field_id = $column[1];
                $builder->selectContents($field_id)->orderBy("values_$field_id.content", $direction);
            } else {
                $builder->orderBy($column[0], $direction);
            }
        }
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

    public function insertContents(array $contents = null)
    {
        if (!$contents)
            return;

        $values = [];
        foreach ($contents as $field_id => $content) {
            $values[] = [
                'entity_id' => $this->id,
                'field_id' => $field_id,
                'content' => (string)$content,
            ];
        }

        Value::insert($values);
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

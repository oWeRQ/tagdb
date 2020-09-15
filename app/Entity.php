<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        'name',
    ];

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'entities_tags');
    }

    public function values()
    {
        return $this->hasMany('App\Value');
    }

    public function scopeQueryJson($query, $json)
    {
        $queryData = json_decode($json, true);

        if ($tags = $queryData['tags'] ?? null) {
            $query->havingTags($tags);
        }

        if ($search = $queryData['search'] ?? null) {
            $query->search($search);
        }

        return $query;
    }

    public function scopeHavingTags($query, array $tags = null)
    {
        return $query->whereHas('tags', function($query) use($tags) {
            $query->whereIn('name', $tags)
                ->groupBy('entity_id')
                ->havingRaw('count(*) = ?', [count($tags)]);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%'.$search.'%');
    }

    public function scopeSort($query, $sort = null)
    {
        if (!$sort)
            return $query->orderBy('created_at', 'desc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            if ($column[0] === 'contents') {
                $query->leftJoin("values as values$i", function($join) use($i, $column) {
                    $join->on("values$i.entity_id", '=', 'entities.id');
                    $join->where("values$i.field_id", '=', $column[1]);
                })->orderBy("values$i.content", $direction);
            } else {
                $query->orderBy($column[0], $direction);
            }
        }

        return $query->select('entities.*');
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
}

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

    public function havingTags(array $tags = null)
    {
        return $this->whereHas('tags', function($query) use($tags) {
            $query->whereIn('name', $tags)
                ->groupBy('entity_id')
                ->havingRaw('count(*) = ?', [count($tags)]);
        });
    }

    public function updateTags(array $tags = null)
    {
        if (!is_array($tags))
            return;

        $this->tags()->sync(array_column($tags, 'id'));
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

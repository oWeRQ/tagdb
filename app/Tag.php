<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    public function entities()
    {
        return $this->belongsToMany('App\Entity', 'entities_tags');
    }

    public function fields()
    {
        return $this->hasMany('App\Field');
    }

    public function scopeOrderByEntities($query)
    {
        $query->withCount('entities')->orderBy('entities_count', 'desc');
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

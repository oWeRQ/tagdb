<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $fillable = [
        'content',
        'entity_id',
        'field_id',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}

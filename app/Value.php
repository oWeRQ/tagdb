<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $fillable = [
        'content',
        'entity_id',
        'tag_id',
    ];

    public function entity()
    {
        return $this->belongsTo('App\Entity');
    }

    public function field()
    {
        return $this->belongsTo('App\Field');
    }
}

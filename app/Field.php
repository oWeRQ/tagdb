<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TagScope;

class Field extends Model
{
    protected $fillable = [
        'tag_id',
        'type',
        'name',
        'code',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TagScope);
    }

    public function tag()
    {
        return $this->belongsTo('App\Tag');
    }
}

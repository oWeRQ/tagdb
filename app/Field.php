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

    public function scopeSort($query, $sort = null)
    {
        if (!$sort)
            return $query->orderBy('name', 'asc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            $query->orderBy($column[0], $direction);
        }

        return $query;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array(
            'user_id' => auth()->user()->id,
        ), true);
        parent::__construct($attributes);
    }

    public function entities()
    {
        return $this->belongsToMany('App\Entity');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function presets()
    {
        return $this->belongsToMany('App\Preset');
    }
}

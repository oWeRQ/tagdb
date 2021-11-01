<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
    ];

    public static function current()
    {
        return static::find(static::getCurrentId());
    }

    public static function getCurrentId() {
        return session('currentProject', 1);
    }

    public static function setCurrentId($project_id)
    {
        session(['currentProject' => $project_id]);
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

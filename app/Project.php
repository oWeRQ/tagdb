<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
    ];

    public static function getCurrent()
    {
        return static::find(session('currentProject', 1));
    }

    public static function setCurrent($project_id)
    {
        session(['currentProject' => $project_id]);
    }
}

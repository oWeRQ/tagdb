<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Scopes\ProjectScope;

class Token extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ProjectScope);
        static::creating(function(Token $model) {
            $model->project_id = request()->project()->id;
            $model->apikey = Str::random(32);
        });
    }

    public static function findByApiKey($apikey) {
        return static::withoutGlobalScope(ProjectScope::class)->where('apikey', $apikey)->first();
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function access()
    {
        return $this->hasMany('App\TokenAccess');
    }

    public function presets()
    {
        return $this->belongsToMany('App\Preset', 'token_preset')->as('access')->withPivot('can_create', 'can_read', 'can_update', 'can_delete');
    }

    public function scopeSort($query, $sort = null)
    {
        if (!$sort)
            return $query->orderBy('id', 'asc');

        foreach (explode(',', $sort) as $i => $part) {
            $column = explode('.', trim($part, '+-'));
            $direction = $part[0] === '-' ? 'desc' : 'asc';

            $query->orderBy($column[0], $direction);
        }

        return $query;
    }

    public function updateAccess(array $access = null)
    {
        if (!is_array($access))
            return;

        foreach ($access as $data) {
            if (!$this->project->hasPresetId($data['preset_id']))
                continue;

            TokenAccess::updateOrCreate([
                'token_id' => $this->id,
                'preset_id' => $data['preset_id'],
            ], [
                'can_create' => $data['can_create'],
                'can_read' => $data['can_read'],
                'can_update' => $data['can_update'],
                'can_delete' => $data['can_delete'],
            ]);
        }
    }
}

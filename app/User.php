<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $_currentProject = null;

    public function getCurrentProjectAttribute()
    {
        if (!$this->_currentProject) {
            $this->_currentProject = Project::find(session('currentProject', $this->allProjects()->first()->id));
        }

        return $this->_currentProject;
    }

    public function switchProject($project) {
        if (!$this->belongsToProject($project)) {
            return false;
        }

        session(['currentProject' => $project->id]);

        return true;
    }

    public function allProjects()
    {
        return $this->ownedProjects->merge($this->projects)->sortBy('name');
    }

    public function ownedProjects()
    {
        return $this->hasMany(Project::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function ownsProject($project)
    {
        if (is_null($project)) {
            return false;
        }

        return $this->id == $project->user_id;
    }

    public function belongsToProject($project)
    {
        return $this->projects->contains(function ($t) use ($project) {
            return $t->id === $project->id;
        }) || $this->ownsProject($project);
    }
}

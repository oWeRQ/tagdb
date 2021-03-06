<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use App\Scopes\UserScope;

class Project extends Model
{
    protected $fillable = [
        'name',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(array(
            'user_id' => optional(auth()->user())->id,
        ), true);
        parent::__construct($attributes);
    }

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new UserScope);
    // }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function updateUsers(array $users = null)
    {
        if (!is_array($users))
            return;

        $ids = collect($users)->mapWithKeys(function($requestUser) {
            $user = User::where('email', $requestUser['email'])->first();
            if (!$user) {
                throw ValidationException::withMessages(['users' => [$requestUser['email'].' does not exist']]);
            }

            return [$user->id => $requestUser['membership']];
        });

        $this->users()->sync($ids);
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

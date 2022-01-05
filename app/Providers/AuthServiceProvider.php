<?php

namespace App\Providers;

use App\User;
use App\Project;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->id === 1) {
                return true;
            }
        });

        Gate::define('admin', function (User $user) {
            return false;
        });

        Gate::define('show-project', function (User $user, Project $project) {
            return $user->belongsToProject($project);
        });

        Gate::define('update-project', function (User $user, Project $project) {
            return $user->belongsToProject($project);
        });

        Gate::define('destroy-project', function (User $user, Project $project) {
            return $user->belongsToProject($project);
        });
    }
}

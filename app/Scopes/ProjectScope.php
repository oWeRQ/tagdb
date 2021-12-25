<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProjectScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('project_id', '=', auth()->user()->current_project_id);
    }
}

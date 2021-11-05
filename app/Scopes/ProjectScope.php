<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Project;

class ProjectScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('project_id', '=', auth()->user()->currentProject->id);
    }
}

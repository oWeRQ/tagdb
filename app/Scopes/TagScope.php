<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TagScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereHas('tag', function($query) {
            $query->where('project_id', '=', auth()->user()->currentProject->id);
        });
    }
}

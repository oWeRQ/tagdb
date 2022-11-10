<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
    protected $table = 'project_user';
}

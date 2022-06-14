<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    protected function paginate($query, $perPage)
    {
        if ($perPage == -1) {
            $results = $query->get();
            return new \Illuminate\Pagination\LengthAwarePaginator($results, $results->count(), -1);
        }

        return $query->paginate($perPage);
    }
}

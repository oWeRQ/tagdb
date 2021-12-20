<?php

namespace App\Http\Controllers\Api\v1;

use App\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TagImportController extends Controller
{
    public function store(Request $request)
    {
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Value;
use App\Http\Resources\ValueResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function index(Request $request)
    {
        return ValueResource::collection(Value::paginate());
    }

    public function show($id)
    {
        return new ValueResource(Value::find($id));
    }
}

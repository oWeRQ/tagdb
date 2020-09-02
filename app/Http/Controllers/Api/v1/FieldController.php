<?php

namespace App\Http\Controllers\Api\v1;

use App\Field;
use App\Http\Resources\EntityResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        return EntityResource::collection(Field::paginate());
    }

    public function show($id)
    {
        return new EntityResource(Field::find($id));
    }
}

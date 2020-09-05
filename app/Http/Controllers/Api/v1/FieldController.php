<?php

namespace App\Http\Controllers\Api\v1;

use App\Field;
use App\Http\Resources\FieldResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        return FieldResource::collection(Field::paginate($perPage));
    }

    public function show($id)
    {
        return new FieldResource(Field::find($id));
    }

    public function store(Request $request)
    {
        return new FieldResource(Field::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $field = Field::findOrFail($id);
        $field->update($request->all());

        return new FieldResource($field);
    }

    public function destroy($id)
    {
        Field::find($id)->delete();

        return 204;
    }
}

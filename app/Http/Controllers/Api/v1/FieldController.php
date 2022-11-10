<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\Entity;
use App\Models\v1\Field;
use App\Http\Resources\FieldResource;
use Illuminate\Http\Request;

class FieldController extends ApiController
{
    public function index(Request $request)
    {
        $query = Field::query();
        $query->sort($request->get('sort'));

        $perPage = $request->get('per_page', -1);

        return FieldResource::collection($this->paginate($query, $perPage));
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

    public function updateValues(Request $request, $id)
    {
        $field = Field::findOrFail($id);
        $contents = [
            $field->id => $request->get('content'),
        ];

        Entity::find($request->get('id'))->each(function($entity) use($contents) {
            $entity->updateContents($contents);
        });

        return response()->noContent();
    }

    public function destroy($id)
    {
        Field::find($id)->delete();

        return 204;
    }
}

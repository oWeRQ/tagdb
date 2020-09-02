<?php

namespace App\Http\Controllers\Api\v1;

use App\Entity;
use App\Http\Resources\EntityResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        return EntityResource::collection(Entity::paginate());
    }

    public function show($id)
    {
        return new EntityResource(Entity::find($id));
    }

    public function store(Request $request)
    {
        return new EntityResource(Entity::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $entity = Entity::findOrFail($id);
        $entity->update($request->all());

        return new EntityResource($entity);
    }

    public function destroy($id)
    {
        Entity::find($id)->delete();

        return 204;
    }
}

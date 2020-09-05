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
        $query = json_decode($request->get('query', '{}'), true);
        $tags = $query['tags'] ?? null;

        $entity = new Entity;
        if ($tags) {
            $entity = $entity->havingTags($tags);
        }

        $perPage = $request->get('per_page', 100);

        return EntityResource::collection($entity->paginate($perPage));
    }

    public function show($id)
    {
        return new EntityResource(Entity::find($id));
    }

    public function store(Request $request)
    {
        $entity = Entity::create($request->all());
        $entity->updateTags($request->get('tags'));
        $entity->updateContents($request->get('contents'));

        return new EntityResource($entity);
    }

    public function update(Request $request, $id)
    {
        $entity = Entity::findOrFail($id);
        $entity->update($request->all());
        $entity->updateTags($request->get('tags'));
        $entity->updateContents($request->get('contents'));

        return new EntityResource($entity);
    }

    public function destroy($id)
    {
        Entity::find($id)->delete();

        return 204;
    }
}

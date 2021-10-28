<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Support\Arr;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Entity;
use App\Preset;

class PublicController extends Controller
{
    public function index(Request $request, $presetName)
    {
        $query = Preset::where('name', $presetName)->firstOrFail()->entityQuery();

        if ($sort = $request->get('sort')) {
            $query->sort($sort);
        }

        return $query->with(['tags', 'values.field'])->get()->map(function($entity) {
            return array_merge([
                'id' => $entity->id,
                'name' => $entity->name,
            ], $entity->valuesAssoc(), [
                'created_at' => $entity->created_at,
                'updated_at' => $entity->updated_at,
                'tags' => $entity->tags->pluck('name')->all(),
            ]);
        });
    }

    public function show(Request $request, $presetName, $entityId)
    {
        $query = Preset::where('name', $presetName)->firstOrFail()->entityQuery();
        $entity = $query->with(['tags', 'values.field'])->findOrFail($entityId);

        return array_merge([
            'id' => $entity->id,
            'name' => $entity->name,
        ], $entity->valuesAssoc(), [
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,
            'tags' => $entity->tags->pluck('name')->all(),
        ]);
    }

    public function store(Request $request, $presetName)
    {
        $preset = Preset::where('name', $presetName)->firstOrFail();
        $entity = Entity::create($request->all());
        $entity->updateTags($preset->tags);

        $values = Arr::except($request->all(), $entity->getVisible());
        $entity->updateValues($values);

        return ['id' => $entity->id];
    }

    public function update(Request $request, $presetName, $entityId)
    {
        $query = Preset::where('name', $presetName)->firstOrFail()->entityQuery();
        $entity = $query->with(['tags', 'values.field'])->findOrFail($entityId);
        $entity->update($request->all());

        $values = Arr::except($request->all(), $entity->getVisible());
        $entity->updateValues($values);

        return 204;
    }

    public function destroy(Request $request, $presetName, $entityId)
    {
        $query = Preset::where('name', $presetName)->firstOrFail()->entityQuery();
        $entity = $query->with(['tags', 'values.field'])->findOrFail($entityId);
        $entity->delete();

        return 204;
    }
}

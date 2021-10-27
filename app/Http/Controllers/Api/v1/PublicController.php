<?php

namespace App\Http\Controllers\Api\v1;

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

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}

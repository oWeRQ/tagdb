<?php

namespace App\Http\Controllers\Token;

use Illuminate\Support\Arr;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Entity;
use App\Token;

class PresetsController extends Controller
{
    protected $preset;

    public function __construct(Request $request)
    {
        $token = Token::findByApiKey($request->bearerToken());
        if (!$token) {
            abort(401);
        }
        $this->preset = $token->presets()->where('name', $request->route('preset'))->firstOrFail();
    }

    public function index(Request $request)
    {
        if (!$this->preset->access->can_read) {
            abort(403);
        }

        $presetTags = $this->preset->tags->pluck('name')->all();

        return $this->preset->entityQuery($request->get('sort'))->with(['tags', 'values.field'])->get()->map(function($entity) use($presetTags) {
            return array_merge([
                'id' => $entity->id,
                'name' => $entity->name,
            ], $entity->valuesAssoc(), [
                'created_at' => $entity->created_at,
                'updated_at' => $entity->updated_at,
                'tags' => $entity->tags->pluck('name')->diff($presetTags)->values()->all(),
            ]);
        });
    }

    public function show(Request $request)
    {
        if (!$this->preset->access->can_read) {
            abort(403);
        }

        $presetTags = $this->preset->tags->pluck('name')->all();
        $entity = $this->preset->entityQuery()->with(['tags', 'values.field'])->findOrFail($request->route('entity'));

        return array_merge([
            'id' => $entity->id,
            'name' => $entity->name,
        ], $entity->valuesAssoc(), [
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,
            'tags' => $entity->tags->pluck('name')->diff($presetTags)->values()->all(),
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->preset->access->can_create) {
            abort(403);
        }

        $entity = Entity::create($request->all());
        $entity->updateTags($this->preset->tags->all());

        $values = Arr::except($request->all(), $entity->getVisible());
        $entity->updateValues($values);

        return ['id' => $entity->id];
    }

    public function update(Request $request)
    {
        if (!$this->preset->access->can_update) {
            abort(403);
        }

        $query = $this->preset->entityQuery();
        $entity = $query->with(['tags', 'values.field'])->findOrFail($request->route('entity'));
        $entity->update($request->all());

        $values = Arr::except($request->all(), $entity->getVisible());
        $entity->updateValues($values);

        return response()->noContent();
    }

    public function destroy(Request $request)
    {
        if (!$this->preset->access->can_delete) {
            abort(403);
        }

        $query = $this->preset->entityQuery();
        $entity = $query->with(['tags', 'values.field'])->findOrFail($request->route('entity'));
        $entity->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;

class TagController extends ApiController
{
    public function index(Request $request)
    {
        $query = Tag::query()->with('fields');

        $with_tags = $request->get('with_tags');

        $query->withCount(['entities' => function($query) use($with_tags) {
            $query->havingTags($with_tags);
        }]);

        if ($with_tags) {
            $query->whereNotIn('name', array_map(function ($value) {
                return trim($value, '+-');
            }, $with_tags));
        }

        $query->search($request->get('search'));
        $query->sort($request->get('sort'));

        if ($export = $request->get('export')) {
            $json_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
            $json = TagResource::collection($query->get())->toJson($json_options);
            return response()->streamDownload(function() use($json) { echo $json; }, $export);
        }

        $perPage = $request->get('per_page', -1);

        return TagResource::collection($this->paginate($query, $perPage));
    }

    public function show($id)
    {
        return new TagResource(Tag::find($id));
    }

    public function store(Request $request)
    {
        $tag = Tag::create($request->all());
        $tag->updateFields($request->get('fields'));

        return new TagResource($tag);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->all());
        $tag->updateFields($request->get('fields'));

        return new TagResource($tag);
    }

    public function attachEntities(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->entities()->sync($request->get('id'), false);

        return response()->noContent();
    }

    public function detachEntities(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->entities()->detach($request->get('id'));

        return response()->noContent();
    }

    public function destroy($id)
    {
        Tag::find($id)->delete();

        return response()->noContent();
    }
}

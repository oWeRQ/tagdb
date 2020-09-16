<?php

namespace App\Http\Controllers\Api\v1;

use App\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        $with_tags = $request->get('with_tags');

        $query->withCount(['entities' => function($query) use($with_tags) {
            $query->havingTags($with_tags);
        }]);

        $query->sort($request->get('sort'));

        $perPage = $request->get('per_page', 100);

        return TagResource::collection($query->paginate($perPage));
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

    public function destroy($id)
    {
        Tag::find($id)->delete();

        return 204;
    }
}

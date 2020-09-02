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
        return TagResource::collection(Tag::paginate());
    }

    public function show($id)
    {
        return new TagResource(Tag::find($id));
    }

    public function store(Request $request)
    {
        return new TagResource(Tag::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->all());

        return new TagResource($tag);
    }

    public function destroy($id)
    {
        Tag::find($id)->delete();

        return 204;
    }
}

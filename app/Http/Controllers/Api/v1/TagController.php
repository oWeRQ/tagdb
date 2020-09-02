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
}

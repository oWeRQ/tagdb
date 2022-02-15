<?php

namespace App\Http\Controllers\Api\v1;

use App\Preset;
use App\Http\Resources\PresetResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class PresetController extends Controller
{
    public function index(Request $request)
    {
        $query = Preset::query();
        $query->sort($request->get('sort'));

        $perPage = $request->get('per_page', 100);

        return PresetResource::collection($query->paginate($perPage));
    }

    public function show($id)
    {
        return new PresetResource(Preset::find($id));
    }

    public function store(Request $request)
    {
        return new PresetResource(Preset::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $preset = Preset::findOrFail($id);
        $preset->update($request->all());

        return new PresetResource($preset);
    }

    public function destroy($id)
    {
        Preset::find($id)->delete();

        return 204;
    }
}

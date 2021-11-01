<?php

namespace App\Http\Controllers\Api\v1;

use App\Project;
use App\Http\Resources\ProjectResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);

        return ProjectResource::collection(Project::paginate($perPage));
    }

    public function show($id)
    {
        return new ProjectResource(Project::find($id));
    }

    public function store(Request $request)
    {
        return new ProjectResource(Project::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $field = Project::findOrFail($id);
        $field->update($request->all());

        return new ProjectResource($field);
    }

    public function destroy($id)
    {
        Project::find($id)->delete();

        return 204;
    }
}

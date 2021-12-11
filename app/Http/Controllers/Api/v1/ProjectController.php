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
        $project = Project::create($request->all());
        $project->updateUsers($request->get('users'));

        return new ProjectResource($project);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        $project->updateUsers($request->get('users'));

        return new ProjectResource($project);
    }

    public function destroy($id)
    {
        Project::find($id)->delete();

        return 204;
    }
}

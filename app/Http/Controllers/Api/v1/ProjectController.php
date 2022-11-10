<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query();
        if ($request->user()->cannot('admin')) {
            $projects->whereIn('id', $request->user()->allProjects()->pluck('id'));
        }

        $perPage = $request->get('per_page', 100);

        return ProjectResource::collection($projects->paginate($perPage));
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('show-project', $project);

        return new ProjectResource($project);
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
        $this->authorize('update-project', $project);

        $project->update($request->all());
        $project->updateUsers($request->get('users'));

        return new ProjectResource($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('destroy-project', $project);

        $project->delete();

        return response()->noContent();
    }
}

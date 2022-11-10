<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\v1\Project;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }

    public function projects(Request $request)
    {
        return ProjectResource::collection($request->user()->allProjects());
    }

    public function currentProject(Request $request)
    {
        return new ProjectResource($request->user()->currentProject);
    }

    public function switchProject(Request $request)
    {
        $project = Project::findOrFail($request->get('id'));

        if (!$request->user()->switchProject($project)) {
            abort(403);
        }

        return response('', 204);
    }
}

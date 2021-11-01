<?php

namespace App\Http\Controllers\Api\v1;

use App\Project;
use App\Http\Resources\ProjectResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class CurrentProjectController extends Controller {
    public function get() {
        return new ProjectResource(Project::current());
    }

    public function set(Request $request) {
        Project::setCurrentId($request->get('id'));
        return response('', 204);
    }
}

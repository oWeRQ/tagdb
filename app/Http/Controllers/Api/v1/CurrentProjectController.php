<?php

namespace App\Http\Controllers\Api\v1;

use App\Project;
use App\Http\Resources\ProjectResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class CurrentProjectController extends Controller {
    public function get() {
        return new ProjectResource(Project::getCurrent());
    }

    public function set(Request $request) {
        Project::setCurrent($request->get('id'));
        return response('', 204);
    }
}

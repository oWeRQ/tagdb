<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use App\Http\Resources\UserResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);

        return UserResource::collection(User::paginate($perPage));
    }

    public function show($id)
    {
        return new UserResource(User::find($id));
    }

    public function store(Request $request)
    {
        return new UserResource(User::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return new UserResource($user);
    }

    public function destroy($id)
    {
        User::find($id)->delete();

        return 204;
    }
}

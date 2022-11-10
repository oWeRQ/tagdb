<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\Value;
use App\Http\Resources\ValueResource;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);

        return ValueResource::collection(Value::paginate($perPage));
    }

    public function show($id)
    {
        return new ValueResource(Value::find($id));
    }

    public function store(Request $request)
    {
        return new ValueResource(Value::create($request->all()));
    }

    public function update(Request $request, $id)
    {
        $value = Value::findOrFail($id);
        $value->update($request->all());

        return new ValueResource($value);
    }

    public function destroy($id)
    {
        Value::find($id)->delete();

        return 204;
    }
}

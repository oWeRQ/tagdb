<?php

namespace App\Http\Controllers\Api\v1;

use App\Token;
use App\Http\Resources\TokenResource;
use Illuminate\Http\Request;

class TokenController extends ApiController
{
    public function index(Request $request)
    {
        $query = Token::query();
        $query->sort($request->get('sort'));

        $perPage = $request->get('per_page', -1);

        return TokenResource::collection($this->paginate($query, $perPage));
    }

    public function show($id)
    {
        return new TokenResource(Token::find($id));
    }

    public function store(Request $request)
    {
        $token = Token::create($request->all());
        $token->updateAccess($request->get('access'));

        return new TokenResource($token);
    }

    public function update(Request $request, $id)
    {
        $token = Token::findOrFail($id);
        $token->update($request->all());
        $token->updateAccess($request->get('access'));

        return new TokenResource($token);
    }

    public function destroy($id)
    {
        Token::find($id)->delete();

        return response()->noContent();
    }
}
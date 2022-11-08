<?php

namespace App\Http\Controllers\Token;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Generators\OpenapiGenerator;
use App\Token;

class OpenapiController extends Controller
{
    protected $token;

    public function __construct(Request $request)
    {
        $token = Token::findByApiKey($request->bearerToken());
        if (!$token) {
            abort(401);
        }

        $this->token = $token;
    }

    public function index(Request $request)
    {
        $openapi = (new OpenapiGenerator)->fromToken($this->token);
        return response()->json($openapi);
    }
}

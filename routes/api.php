<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function ($route) {
    $route->resource('entities', 'Api\v1\EntityController');
    $route->resource('tags', 'Api\v1\TagController');
    $route->resource('fields', 'Api\v1\FieldController');
    $route->resource('values', 'Api\v1\ValueController');
    $route->resource('presets', 'Api\v1\PresetController');
    $route->resource('import', 'Api\v1\ImportController', ['only' => ['store']]);
});

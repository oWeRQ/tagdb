<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1 as ApiV1;

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

Route::prefix('v1')->middleware('auth')->group(function() {
    Route::get('/account', function (Request $request) {
        return $request->user();
    });
    Route::get('current-project', [ApiV1\CurrentProjectController::class, 'get']);
    Route::put('current-project', [ApiV1\CurrentProjectController::class, 'set']);
    Route::apiResource('entities', 'Api\v1\EntityController');
    Route::apiResource('tags', 'Api\v1\TagController');
    Route::apiResource('fields', 'Api\v1\FieldController');
    Route::apiResource('values', 'Api\v1\ValueController');
    Route::apiResource('presets', 'Api\v1\PresetController');
    Route::apiResource('projects', 'Api\v1\ProjectController');
    Route::apiResource('users', 'Api\v1\UserController');
    Route::apiResource('import', 'Api\v1\ImportController', ['only' => ['store']]);
    Route::apiResource('public/{preset}', 'Api\v1\PublicController', ['parameters' => ['{preset}' => 'entity']]);
});

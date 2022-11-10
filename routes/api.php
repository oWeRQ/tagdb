<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

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
    Route::get('account', [Api\v1\AccountController::class, 'index']);
    Route::get('account/projects', [Api\v1\AccountController::class, 'projects']);
    Route::get('account/current-project', [Api\v1\AccountController::class, 'currentProject']);
    Route::post('account/switch-project', [Api\v1\AccountController::class, 'switchProject']);
    Route::apiResource('entities', Api\v1\EntityController::class);
    Route::delete('entities', [Api\v1\EntityController::class, 'destroyMany']);
    Route::apiResource('tags', Api\v1\TagController::class);
    Route::post('tags/{id}/entities', [Api\v1\TagController::class, 'attachEntities']);
    Route::delete('tags/{id}/entities', [Api\v1\TagController::class, 'detachEntities']);
    Route::apiResource('tags-import', Api\v1\TagImportController::class, ['only' => ['store']]);
    Route::apiResource('tokens', Api\v1\TokenController::class);
    Route::get('tokens/{id}/openapi', [Api\v1\TokenController::class, 'openapi'])->name('openapi');
    Route::apiResource('fields', Api\v1\FieldController::class);
    Route::put('fields/{id}/values', [Api\v1\FieldController::class, 'updateValues']);
    Route::apiResource('presets', Api\v1\PresetController::class);
    Route::apiResource('projects', Api\v1\ProjectController::class);
    Route::apiResource('import', Api\v1\ImportController::class, ['only' => ['store']]);

    Route::middleware('can:admin')->group(function() {
        Route::apiResource('users', Api\v1\UserController::class);
        Route::apiResource('values', Api\v1\ValueController::class);
    });
});
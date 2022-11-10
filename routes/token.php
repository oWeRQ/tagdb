<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Token\OpenapiController;
use App\Http\Controllers\Token\PresetsController;

Route::get('openapi', [OpenapiController::class, 'index']);
Route::apiResource('presets/{preset}', PresetsController::class, ['parameters' => ['{preset}' => 'entity']]);

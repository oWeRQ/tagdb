<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Token\OpenapiController;

Route::get('openapi', [OpenapiController::class, 'index']);
Route::apiResource('presets/{preset}', 'Token\PresetsController', ['parameters' => ['{preset}' => 'entity']]);

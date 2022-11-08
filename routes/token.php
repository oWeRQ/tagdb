<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('presets/{preset}', 'Token\PresetsController', ['parameters' => ['{preset}' => 'entity']]);

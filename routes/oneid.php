<?php

use Aslnbxrz\OneId\Http\Controllers\OneIDController;
use Illuminate\Support\Facades\Route;

Route::controller(OneIDController::class)
    ->middleware(config('oneid.routes.middleware', 'web'))
    ->prefix(config('oneid.routes.prefix', 'auth/oneid'))
    ->group(function () {
        Route::post('handle', 'handle')->name(config('oneid.routes.names.handle', 'oneid.handle'));
        Route::post('logout', 'logout')->name(config('oneid.routes.names.logout', 'oneid.logout'));
        Route::get('redirect', 'redirect')->name(config('oneid.routes.names.redirect', 'oneid.redirect'));
    });

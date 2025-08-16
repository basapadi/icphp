<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('entry');
});

Route::prefix('auth')->group(function () {
    Route::middleware('auth:sanctum')->controller(MenuController::class)->group(function () {
        Route::get('menus', 'getMenu');
    });
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::middleware('auth:sanctum')->group(function(){
            Route::get('me', 'me');
            Route::post('logout', 'logout');
        });
    });

    
});

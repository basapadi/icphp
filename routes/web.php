<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::controller(MenuController::class)->group(function () {
        Route::get('menus', 'getMenu');
    });
});

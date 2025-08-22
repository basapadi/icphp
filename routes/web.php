<?php

use App\Http\Controllers\{
    AuthController,
    DataMenuController,
    ItemController,
    MenuController,
    UnitController,
    UserController,
    RoleController,
    ContactController,
    ReceivedItemController
};
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('entry');
})->where('any', '^(?!api).*$');

Route::prefix('api')->group(function () {
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
    Route::controller(UserController::class)->middleware('auth:sanctum')->prefix('user')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
    Route::controller(UnitController::class)->middleware('auth:sanctum')->prefix('unit')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
    Route::controller(ItemController::class)->middleware('auth:sanctum')->prefix('item')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
    Route::controller(DataMenuController::class)->middleware('auth:sanctum')->prefix('data-menu')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
    Route::controller(RoleController::class)->middleware('auth:sanctum')->prefix('role')->group(function () {
        Route::get('grid', 'grid');
        Route::put('update/{id}', 'update');
    });
    Route::controller(ContactController::class)->middleware('auth:sanctum')->prefix('contact')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
    Route::controller(ReceivedItemController::class)->middleware('auth:sanctum')->prefix('receive')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
});

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
    ReceivedItemController,
    SaleItemController,
    StockController,
    DashboardController,
    DatabaseController,
    PayableController,
    ReceivableController,
    ExpenseController,
    PurchaseOrderController,
    SaleOrderController,
    StockAdjustmentController,
    TrashController
};
use App\Http\Controllers\Tasks\ApprovalPurchaseOrderController;
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
            Route::get('init', 'init');
            Route::post('test', 'test');
            Route::post('save-local-config', 'saveLocalConfig');
            Route::middleware('auth:sanctum')->group(function(){
                Route::get('me', 'me');
                Route::post('logout', 'logout');
            });
        });
    });
    Route::controller(UserController::class)->middleware('auth:sanctum')->prefix('user')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
        Route::delete('/{id}', 'delete');
        Route::post('/', 'store');
        Route::get('/edit/{id}', 'edit');
    });
    Route::controller(UnitController::class)->middleware('auth:sanctum')->prefix('unit')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
        Route::delete('/{id}', 'delete');
        Route::post('/', 'store');
        Route::get('/edit/{id}', 'edit');
    });
    Route::controller(ItemController::class)->middleware('auth:sanctum')->prefix('item')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
        Route::delete('/{id}', 'delete');
        Route::post('/', 'store');
        Route::get('/edit/{id}', 'edit');
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
        Route::delete('/{id}', 'delete');
        Route::post('/', 'store');
        Route::get('/edit/{id}', 'edit');
    });
    Route::controller(ReceivedItemController::class)->middleware('auth:sanctum')->prefix('receive')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
        Route::delete('/{id}', 'delete');
        Route::get('/edit/{id}', 'edit');
        Route::post('/', 'store');
    });
    Route::controller(PurchaseOrderController::class)->middleware('auth:sanctum')->prefix('purchase')->group(function () {
        Route::prefix('order')->group(function(){
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::delete('/{id}', 'delete');
            Route::post('/', 'store');
            Route::get('/edit/{id}', 'edit');
            Route::get('/send-email', 'sendEmail')->name('api.purchase.order.sendEmail');
            Route::post('/create-received-item', 'createReceivedItem')->name('api.purchase.order.createReceivedItem');
            Route::get('/receive/form', 'receivedForm')->name('api.purchase.order.receivedForm');
            Route::post('/need-approval', 'needApproval')->name('api.purchase.order.needApproval');

        });
    });
    Route::controller(SaleOrderController::class)->middleware('auth:sanctum')->prefix('sale')->group(function () {
        Route::prefix('order')->group(function(){
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::delete('/{id}', 'delete');
            Route::post('/', 'store');
            Route::get('/edit/{id}', 'edit');
        });
    });
    Route::controller(SaleItemController::class)->middleware('auth:sanctum')->prefix('sale')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
        Route::delete('/{id}', 'delete');
    });
    Route::controller(StockController::class)->middleware('auth:sanctum')->prefix('stock')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
    });
    Route::controller(DashboardController::class)->middleware('auth:sanctum')->prefix('dashboard')->group(function () {
        Route::get('data', 'data');
    });
    Route::controller(StockAdjustmentController::class)->middleware('auth:sanctum')->prefix('adjustment')->group(function () {
        Route::get('grid', 'grid');
        Route::get('form', 'form');
        Route::delete('/{id}', 'delete');
        Route::post('/', 'store');
        Route::get('/edit/{id}', 'edit');
    });
    Route::controller(PayableController::class)->middleware('auth:sanctum')->prefix('payable')->group(function () {
        Route::get('grid', 'grid');
    });
    Route::controller(ReceivableController::class)->middleware('auth:sanctum')->prefix('receivable')->group(function () {
        Route::get('grid', 'grid');
    });
    Route::controller(ExpenseController::class)->middleware('auth:sanctum')->prefix('expense')->group(function () {
        Route::get('grid', 'grid');
    });

    Route::controller(TrashController::class)->middleware('auth:sanctum')->prefix('trash')->group(function () {
        Route::get('grid', 'grid');
        Route::delete('truncate', 'truncate');
        Route::delete('/{id}', 'delete');   
    });

    Route::controller(DatabaseController::class)->middleware('auth:sanctum')->prefix('database')->group(function () {
        Route::get('edit', 'form');
        Route::post('test', 'test');
        Route::post('save-local-config', 'saveLocalConfig');
        Route::post('run-command', 'runCommand');
    });

    Route::middleware('auth:sanctum')->prefix('task')->group(function () {
        Route::controller(ApprovalPurchaseOrderController::class)->prefix('approval-purchase-order')->group(function () {
            Route::get('grid', 'grid');
            Route::post('/approval', 'approval')->name('api.task.approval-purchase-order.approval');
        });
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    MenuController,
    CommonController,
    DataMenuController,
    UserController,
    RoleController,
    UnitController,
    ItemController,
    ContactController,
    ReceivedItemController,
    PurchaseOrderController,
    SaleOrderController,
    SaleItemController,
    StockController,
    DashboardController,
    StockAdjustmentController,
    PayableController,
    ReceivableController,
    ExpenseController,
    TrashController,
    SettingController,
    DatabaseController,
    ReportController,
    PurchaseInvoiceController,
    PurchasePaymentController
};
use App\Http\Controllers\Tasks\ApprovalPurchaseOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend entry (non-API)
Route::get('/{any}', fn() => view('entry'))
    ->where('any', '^(?!api).*$');


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        // Public endpoints
        Route::controller(AuthController::class)->group(function () {
            Route::post('login', 'login');
            Route::get('init', 'init');
            Route::post('test', 'test');
            Route::post('save-local-config', 'saveLocalConfig');
        });

        // Protected endpoints
        Route::middleware('auth:sanctum')->group(function () {
            Route::controller(AuthController::class)->group(function () {
                Route::get('me', 'me');
                Route::post('logout', 'logout');
            });

            Route::controller(MenuController::class)->group(function () {
                Route::get('menus', 'getMenu');
            });

            Route::controller(CommonController::class)->group(function () {
                Route::get('change-log', 'ChangeLog');
            });
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Protected API Routes (auth:sanctum)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |----------------------------------------------------------
        | Master Data Routes
        |----------------------------------------------------------
        */
        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
        });

        Route::controller(UnitController::class)->prefix('unit')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
        });

        Route::controller(ItemController::class)->prefix('item')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
        });

        Route::controller(DataMenuController::class)->prefix('data-menu')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
        });

        Route::controller(RoleController::class)->prefix('role')->group(function () {
            Route::get('grid', 'grid');
            Route::put('update/{id}', 'update');
        });

        Route::controller(ContactController::class)->prefix('contact')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
        });


        /*
        |----------------------------------------------------------
        | Purchase & Receive
        |----------------------------------------------------------
        */
        Route::controller(PurchaseOrderController::class)->prefix('purchase/order')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');

            // Custom endpoints
            Route::post('/send-po', 'sendPo')->name('api.purchase.order.sendPo');
            Route::post('/create-received-item', 'createReceivedItem')->name('api.purchase.order.createReceivedItem');
            Route::get('/receive/form', 'receivedForm')->name('api.purchase.order.receivedForm');
            Route::post('/need-approval', 'needApproval')->name('api.purchase.order.needApproval');
            Route::post('/download-po', 'downloadPo')->name('api.purchase.order.downloadPo');
        });

        Route::controller(ReceivedItemController::class)->prefix('receive')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');

            Route::post('/create-invoice', 'createInvoice')->name('api.purchase.invoice.createInvoice');
            Route::get('/invoice/form', 'invoiceForm')->name('api.purchase.invoice.form');
        });

        Route::controller(PurchaseInvoiceController::class)->prefix('purchase/invoice')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
            Route::post('/create-payment', 'createPayment')->name('api.purchase.invoice.createPayment');
            Route::get('/invoice/payment-form', 'paymentForm')->name('api.purchase.invoice.paymentForm');
            Route::post('/open-payment', 'openPayment')->name('api.purchase.invoice.openPayment');
            Route::post('/void-payment', 'voidPayment')->name('api.purchase.invoice.voidPayment');
        });

         Route::controller(PurchasePaymentController::class)->prefix('purchase/payment')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
         });

        /*
        |----------------------------------------------------------
        | Sales
        |----------------------------------------------------------
        */
        Route::controller(SaleOrderController::class)->prefix('sale/order')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
        });

        Route::controller(SaleItemController::class)->prefix('sale')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::delete('/{id}', 'delete');
        });


        /*
        |----------------------------------------------------------
        | Stock & Inventory
        |----------------------------------------------------------
        */
        Route::controller(StockController::class)->prefix('stock')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
        });

        Route::controller(StockAdjustmentController::class)->prefix('adjustment')->group(function () {
            Route::get('grid', 'grid');
            Route::get('form', 'form');
            Route::get('/edit/{id}', 'edit');
            Route::post('/', 'store');
            Route::delete('/{id}', 'delete');
        });


        /*
        |----------------------------------------------------------
        | Financial Modules
        |----------------------------------------------------------
        */
        Route::controller(PayableController::class)->prefix('payable')->group(function () {
            Route::get('grid', 'grid');
        });

        Route::controller(ReceivableController::class)->prefix('receivable')->group(function () {
            Route::get('grid', 'grid');
        });

        Route::controller(ExpenseController::class)->prefix('expense')->group(function () {
            Route::get('grid', 'grid');
        });


        /*
        |----------------------------------------------------------
        | Utility & System
        |----------------------------------------------------------
        */
        Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
            Route::get('data', 'data');
        });

        Route::controller(TrashController::class)->prefix('trash')->group(function () {
            Route::get('grid', 'grid');
            Route::delete('truncate', 'truncate');
            Route::delete('/{id}', 'delete');
        });

        Route::controller(SettingController::class)->prefix('setting')->group(function () {
            Route::get('all', 'all');
            Route::post('save', 'save');
        });

        Route::controller(DatabaseController::class)->prefix('database')->group(function () {
            Route::get('edit', 'form');
            Route::post('test', 'test');
            Route::post('save-local-config', 'saveLocalConfig');
            Route::post('run-command', 'runCommand');
        });

        Route::controller(CommonController::class)->prefix('common')->group(function () {
            Route::post('save-columns', 'saveColumns');
        });

        /*
        |----------------------------------------------------------
        | Tasks / Approvals
        |----------------------------------------------------------
        */
        Route::prefix('task')->group(function () {
            Route::controller(ApprovalPurchaseOrderController::class)
                ->prefix('approval-purchase-order')
                ->group(function () {
                    Route::get('grid', 'grid');
                    Route::post('/approval', 'approval')->name('api.task.approval-purchase-order.approval');
                });
        });

        /*
        |----------------------------------------------------------
        | Reports
        |----------------------------------------------------------
        */
        Route::controller(ReportController::class)->prefix('report')->group(function () {
            Route::get('grid', 'grid');
            Route::get('queries', 'queries');
            Route::get('get-schemas', 'getSchemas');
            Route::post('preview', 'preview');
            Route::post('save-query', 'saveQuery');
            Route::delete('/{id}', 'delete');
            Route::delete('delete-query/{name}', 'deleteQuery');
        });
    });
});

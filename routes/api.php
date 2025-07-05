<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/* use App\Http\Controllers\ItemOrderController; */
/* use App\Http\Controllers\PartyCakesController; */
/* use App\Http\Controllers\PickListController; */
/* use App\Http\Controllers\ReportController; */
/* use App\Http\Controllers\SpecialOrdersController; */
/* use App\Http\Controllers\DispatchInventory1Controller; */
/* use App\Http\Controllers\OpicController; */

use App\Http\Controllers\{
    HomeController,
    DispatchInventoryController,
    DispatchInventory1Controller,
    OpicController,
    OpicManageController,
    SpecialOrdersController,
    PickListController,
    PickListManageController,
    ReportController,
    ItemsController,
    ItemsManageController,
    ItemOrderController,
    StoreController,
    AnnouncementController,
    PartyCakesController,
    PickListCakesController,
    PartyCakesManageController,
    POSController,
    UpdateOrderController,
    PostingController,
    RegisterController,
    BarcodesController,
    BarcodesetupsController,
    CustomersController,
    DiscblankOpController,
    InventitemBarcodeController,
    InventTransacController,
    JournaltransController,
    InventJournalTablesController,
    InventtransreasonController,
    LedgerController,
    NumbersequenceContoller,
    NumbervaluesController,
    POSdiscountlinesController,
    posmmlinegroupsController,
    POSperiodicdiscountController,
    POSvalidController,
    RBOInverntoryController,
    RBOSpecialgroupController,
    RBOStoretableController,
    RboTransacdisController,
    TaxController,
    UnitTableController,
    OrderController,
    MobileOrderController
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/update-counted-value', [ItemOrderController::class, 'updateCountedValue']);
Route::post('/update-all-counted-values', [ItemOrderController::class, 'updateAllCountedValues']);
Route::post('/update-all-counted-values3', [ItemOrderController::class, 'updateAllCountedValues3']);
Route::post('/dispatch/update-all-counted-values', [DispatchInventory1Controller::class, 'updateAllCountedValues']);
Route::post('/sp-update-all-counted-values', [SpecialOrdersController::class, 'updateAllCountedValues']);

/*<==================PICKLIST====================>*/
Route::resource('picklist', PickListController::class);
/* Route::post('/update-adjustment', [PickListController::class, 'updateAdjustment'])->name('update.adjustment'); */
Route::post('/update-actual', [PickListController::class, 'updateActual']);

/*<==================FGCOUNT====================>*/
Route::post('/update-mgcount', [ReportController::class, 'updateMGCount']);
Route::post('/update-counted', [ReportController::class, 'updateCounted']);
Route::post('/save-all-data', [ReportController::class, 'saveAllData'])->name('save.all.data');

Route::get('/api/stores', [PicklistController::class, 'getStores']);
Route::get('/api/picklist/{storeName}', [PicklistController::class, 'getStoreData']);

/*<==================CRATES====================>*/
Route::post('/update-crates-counts', [OpicController::class, 'updateCratesCounts']);
Route::post('/api/get-inventory-data', [OpicController::class, 'getInventoryData']);

/*<==================Partycakes====================>*/
Route::apiResource('partys-cakes', PartycakesController::class);
Route::get('/cakes/{cakeId}/download', [PartyCakesController::class, 'downloadpartycakes'])->name('cake.download');

Route::apiResource('order', OrderController::class);

Route::get('/get-txt-files/{date}', [ReportController::class, 'getTxtFiles']);
Route::get('/get-stores-with-orders/{date}', [ReportController::class, 'getStoresWithOrders']);


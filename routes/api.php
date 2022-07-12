<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SKUController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\CycleCountController;
use App\Models\CycleCounting;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['api']], function () {

    //users
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    //Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    //cycle-counting
    Route::get('/cycle-counts', [CycleCountController::class, 'index']);
    // Route::get('/cycle-counts', [CycleCountController::class, 'show']);

    Route::post('/cycle-count', [CycleCountController::class, 'store']);
    Route::post('/approve-cycle-count', [CycleCountController::class, 'approveCycleCount']);
    Route::post('/reject-cycle-count', [CycleCountController::class, 'rejectCycleCount']);

    Route::get('inventories', [InventoryController::class, 'index']);
    Route::get('inventory/{id}', [InventoryController::class, 'show']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::put('/inventory/{id}', [InventoryController::class, 'update']);
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']);


    Route::get('/getStaffByWarehouse/{warehouseId}', [UserController::class, 'getStaffByWarehouse']);
    Route::get('/getInvByWarehouse/{warehouseId}', [InventoryController::class, 'getInvByWarehouse']);

    // stock
    Route::get('/stocks', [StockController::class, 'index']);
    Route::post('stock', [StockController::class, 'store']);
    Route::get('/getStocksByInventory/{id}', [StockController::class, 'getStocksByInventory']);

    //scheduling
    Route::put('/storeCycleCountingSettings/{warehouseId}', [WarehouseController::class, 'storeCycleCountingSettings']);
    Route::post('/sku', [SKUController::class, 'store']);
    Route::post('/schedule', [ScheduleController::class, 'store']);

    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/getSchedulesByStaff/{staffId}', [ScheduleController::class, 'getSchedulesByStaff']);

    //warehouse
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/warehouse/{id}', [WarehouseController::class, 'show']);
    Route::post('/warehouse', [WarehouseController::class, 'store']);
    Route::put('/warehouse/{id}', [WarehouseController::class, 'update']);



    //category
    Route::get('/categories', [CategoryController::class, 'index']);
});

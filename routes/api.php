<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SKUController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\CycleCountController;

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

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    //cycle-counting
    Route::get('/cycle-counts/{warehouseId}', [CycleCountController::class, 'index']);
    Route::get('/cycle-counts', [CycleCountController::class, 'show']);

    Route::post('/cycle-count', [CycleCountController::class, 'store']);

    Route::get('inventories', [InventoryController::class, 'index']);
    Route::get('inventory/{id}', [InventoryController::class, 'show']);
    Route::get('/getStaffByWarehouse/{warehouseId}', [UserController::class, 'getStaffByWarehouse']);
    Route::get('/getInvByWarehouse/{warehouseId}', [InventoryController::class, 'getInvByWarehouse']);

    // check in out stock
    Route::post('stock', [StockController::class, 'store']);

    //scheduling
    Route::put('/storeCycleCountingSettings/{warehouseId}', [WarehouseController::class, 'storeCycleCountingSettings']);
    Route::post('/sku', [SKUController::class, 'store']);
    Route::post('/schedule', [ScheduleController::class, 'store']);

    Route::get('/schedules/{warehouseId}', [ScheduleController::class, 'index']);
    Route::get('/getSchedulesByStaff/{staffId}', [ScheduleController::class, 'getSchedulesByStaff']);
});

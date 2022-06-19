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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::get('inventories', [InventoryController::class, 'index']);

Route::get('/getStaffByWarehouse/{warehouseId}', [UserController::class, 'getStaffByWarehouse']);
Route::get('/getInvByWarehouse/{warehouseId}', [InventoryController::class, 'getInvByWarehouse']);

// check in out stock
Route::post('stock', [StockController::class, 'store']);

//Authentication
Route::post('/login', [AuthController::class, 'login']);


//scheduling
Route::put('/storeCycleCountingSettings/{warehouseId}', [WarehouseController::class, 'storeCycleCountingSettings']);
Route::post('/sku', [SKUController::class, 'store']);
Route::post('/schedule/{warehouseId}', [ScheduleController::class, 'store']);

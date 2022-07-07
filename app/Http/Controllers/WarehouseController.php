<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use PDO;

class WarehouseController extends Controller
{
    public function storeCycleCountingSettings(Request $request, $warehouseId)
    {
        $data_to_save = [
            'working_day_start' => $request->workday_start,
            'working_day_end' => $request->workday_end,
            'cycle_count_class' => $request->cycle_count_class,
            'warehouse_id' => $warehouseId,
            'staff_ids' => $request->staffs_assigned,
            'inventory_ids' => $request->inventories,
        ];

        $warehouse = Warehouse::findOrFail($warehouseId);
        $warehouse->cycle_counting_settings = json_encode($data_to_save);
        $warehouse->save();
        return $warehouse;
    }

    public function show($id)
    {
        return new WarehouseResource(Warehouse::findOrFail($id));
    }

    public function index()
    {
        return WarehouseResource::collection(Warehouse::all());
    }
}

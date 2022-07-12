<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use App\Models\User;
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

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "location" => "required"
        ]);
        return Warehouse::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $request->validate([
            "location" => "required",
            "manager_id" => "required",
        ]);
        $user = User::findOrFail($request["manager_id"]);
        $user["role"] = "Manager";
        $user->save();
        $warehouse["manager_id"] = $request["manager_id"]; //manager id is not mass-assign since warehouse can be created without a manager
        $warehouse->save();
        return $warehouse->update($request->all());
    }
}

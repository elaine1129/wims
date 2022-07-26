<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Support\Arr;
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
            'start_end_date' => $request->start_end_date
        ];

        $warehouse = Warehouse::findOrFail($warehouseId);
        $warehouse->cycle_counting_settings = json_encode($data_to_save);
        $warehouse->save();
        return $warehouse;
    }

    public function show($id)
    {
        $warehouse = new WarehouseResource(Warehouse::findOrFail($id));
        $new_bins = array();
        foreach ((array) $warehouse["storage_bins"] as $bin) {
            $bin['category'] = Category::findOrFail($bin['category_id']);
            if (($bin['inventory_id'] != -1) && ($bin['inventory_id'] != null)) {
                $bin['inventory'] = Inventory::findOrFail($bin['inventory_id']);
            } else {
                $bin['inventory'] = null;
            }
            array_push($new_bins, $bin);
        }
        $warehouse["storage_bins"] = $new_bins;
        return $warehouse;
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

    public function assignCategoryToBin(Request $request, $id)
    {
        $request->validate([
            "category_id" => "required",
            "storage_bins" => "required|min:1"
        ]);
        $warehouse = Warehouse::findOrFail($id);
        $new_bins = $warehouse->storage_bins;

        foreach ($request->storage_bins as $bin) {

            $temp = array_column($new_bins, 'bin_number');
            $found_key = array_search($bin, $temp);
            $new_bins[$found_key]["category_id"] = $request->category_id;
            if ($new_bins[$found_key]["inventory_id"] != -1 && $new_bins[$found_key]["inventory_id"] != null) {
                $inventory = Inventory::findOrFail($new_bins[$found_key]["inventory_id"]);
                $inventory->category_id = null;
                $inventory->save();
            }

            $new_bins[$found_key]["inventory_id"] = -1;
        }
        $warehouse->storage_bins = $new_bins;
        $warehouse->save();
        return $warehouse;
    }

    public function editStorageBinInventory(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $new_bins = $warehouse->storage_bins;
        $temp = array_column($new_bins, 'bin_id');
        $found_key = array_search($request->bin_id, $temp);
        //REMOVE ORIGINAL INVENTORY IN THE BIN
        if ($new_bins[$found_key]["inventory_id"] != -1 && $new_bins[$found_key]["inventory_id"] != null) {
            $inventory = Inventory::findOrFail($new_bins[$found_key]["inventory_id"]);
            $inventory["category_id"] = null;
            $inventory->save();
        }
        if ($request->inventory_id == null) {
            $new_bins[$found_key]["inventory_id"] = -1;
        } else {
            $inventory = Inventory::findOrFail($request->inventory_id);
            $inventory["category_id"] = $request->category_id;
            $inventory->save();
            $new_bins[$found_key]["inventory_id"] = $request->inventory_id;
        }
        $warehouse->storage_bins = $new_bins;
        $warehouse->save();
        return $warehouse;
    }
}

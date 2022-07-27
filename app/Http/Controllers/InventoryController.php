<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        if (Gate::allows('isStaff') || Gate::allows('isManager')) {
            return InventoryResource::collection(Inventory::where('warehouse_id', Auth::user()->warehouse_id)->get());
        }
        if (Gate::allows('isAdmin')) {
            $inventories = Inventory::all();
            $resouce = InventoryResource::collection($inventories);
            // dd($resouce);
            return $resouce;
        }
        return abort(403);
    }

    public function unassignedCategoryInventories()
    {
        $inventories = Inventory::where('category_id', null)->get();
        return InventoryResource::collection($inventories);
    }

    public function show($id)
    {
        $inventory = Inventory::findOrFail($id);
        return new InventoryResource($inventory);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => 'required',
            'warehouse_id' => 'required',
            'cost_per_unit' => 'required|gt:0',
            'qty_on_hand' => 'required|gt:0',
            // 'category_id' => 'required'
            'priority' => 'required'
        ]);
        $available_bins = array();
        if ($request->category_id != null) {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);
            (array)$available_bins = Arr::where((array)$warehouse->storage_bins, function ($value, $key) use ($request) {
                return ($value['category_id'] == $request->category_id) && ($value["inventory_id"] == -1 || $value["inventory_id"] == null);
            });
            if (count((array)$available_bins) <= 0) {
                return response()->json([
                    'errors' => ['There are no available bin for this category anymore, Please empty a bin for this category first.']
                ], 422);
            } else {
                $inventory = Inventory::create($request->all());
                $new_bins = $warehouse->storage_bins;
                $temp = array_column($new_bins, 'bin_number');
                $found_key = array_search(array_values($available_bins)[0]["bin_number"], $temp);
                $new_bins[$found_key]["inventory_id"] = $inventory->id;
                $warehouse->storage_bins = $new_bins;
                $warehouse->save();
                return $new_bins[$found_key];
            }
        } else {
            return Inventory::create($request->all());
        }
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $request->validate([
            "name" => 'required',
            'cost_per_unit' => 'required|gt:0',
            // 'category_id' => 'required',
            'priority' => 'required'
        ]);
        if (($request->category_id != null) && ($request->category_id != $inventory->category_id)) {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);

            (array)$available_bins = Arr::where((array)$warehouse->storage_bins, function ($value, $key) use ($request) {
                return ($value['category_id'] == $request->category_id) && ($value["inventory_id"] == -1 || $value["inventory_id"] == null);
            });
            if (count((array)$available_bins) <= 0) {
                return response()->json([
                    'errors' => ['There are no available bin for this category anymore, Please empty a bin for this category first.']
                ], 422);
            } else {
                $inventory->update($request->all());
                $new_bins = $warehouse->storage_bins;
                $temp = array_column($new_bins, 'bin_number');
                $found_key = array_search(array_values($available_bins)[0]["bin_number"], $temp);
                $new_bins[$found_key]["inventory_id"] = $inventory->id;
                $warehouse->storage_bins = $new_bins;
                $warehouse->save();
                return $new_bins[$found_key];
            }
        }
        return $inventory->update($request->all());
    }

    public function destroy(Request $request, $id)
    {
        return Inventory::destroy($id);
    }
    public function getInvByWarehouse($warehouseId)
    {
        return InventoryResource::collection(Inventory::where('warehouse_id', '=', $warehouseId)->get());
    }

    // public function appendStorageBin($inventories)
    // {
    //     foreach ($inventories as $inventory) {
    //         // dd($inventory);
    //         $storage_bins_dict = json_decode($inventory->warehouse_id->storage_bins);
    //         foreach ($storage_bins_dict as $key => $value) {
    //             if ($value["inventory_id"] == $inventory->id) {
    //                 $inventory["storage_bin"] = $key;
    //                 break;
    //             }
    //         }
    //     }
    //     return $inventories;
    // }
}

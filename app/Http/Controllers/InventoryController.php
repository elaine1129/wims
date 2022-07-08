<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
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
            'category_id' => 'required'
        ]);
        return Inventory::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $request->validate([
            "name" => 'required',
            'cost_per_unit' => 'required|gt:0',
            'category_id' => 'required'
        ]);
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

<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = InventoryResource::collection(Inventory::all());
        return $inventories;
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

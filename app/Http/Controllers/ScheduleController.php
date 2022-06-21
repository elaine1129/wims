<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CycleCountSchedule;
use App\Models\Inventory;
use App\Models\Sku;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $dataArray = $request->all();
        foreach ($dataArray as &$data) {
            $inventory = Inventory::findOrFail($data['inventory_id']);
            $sku_id = $inventory->sku->id;
            $data["sku_id"] = $sku_id;
            unset($data["inventory_id"]);
        }
        unset($data);

        return CycleCountSchedule::insert($dataArray);
    }
}

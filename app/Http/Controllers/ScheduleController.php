<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CycleCountSchedule;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;

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

    public function index($warehouseId)
    {

        $data = CycleCountSchedule::where(function ($query) use ($warehouseId) {
            $query->whereHas('staff', function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            });
        })->get();
        return ScheduleResource::collection($data);
    }

    public function getSchedulesByStaff($staffId)
    {
        return ScheduleResource::collection(CycleCountSchedule::where('staff_id', $staffId)->get());
    }
}

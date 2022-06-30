<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CycleCountSchedule;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

    public function index()
    {
        // dd(auth()->user());
        if (Gate::allows('isStaff')) {
            return ScheduleResource::collection(CycleCountSchedule::where('staff_id', Auth::id())->get());
        } else if (Gate::allows('isManager')) {
            $data = CycleCountSchedule::where(function ($query) {
                $query->whereHas('staff', function ($q) {
                    $q->where('warehouse_id', Auth::user()->warehouse_id);
                });
            })->get();
            return ScheduleResource::collection($data);
        } else {
            abort(403);
        }

        // if (Auth::user()->role == 'Staff') {
        //     return ScheduleResource::collection(CycleCountSchedule::where('staff_id', Auth::id())->get());
        // } else if (Auth::user()->role == 'Manager') {
        //     $data = CycleCountSchedule::where(function ($query) {
        //         $query->whereHas('staff', function ($q) {
        //             $q->where('warehouse_id', Auth::user()->warehouse_id);
        //         });
        //     })->get();
        //     return ScheduleResource::collection($data);
        // }
    }

    public function getSchedulesByStaff()
    {
        return ScheduleResource::collection(CycleCountSchedule::where('staff_id', Auth::id())->get());
    }
}

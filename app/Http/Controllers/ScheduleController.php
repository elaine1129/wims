<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleResource;
use App\Models\CycleCounting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CycleCountSchedule;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Warehouse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        //delete all reports and schedules
        CycleCounting::where(function ($query) {
            $query->whereHas('schedule', function ($q) {
                $q->whereHas('staff', function ($r) {
                    $r->where('warehouse_id', Auth::user()->warehouse_id);
                });
            });
        })->delete();
        CycleCountSchedule::where(function ($query) {
            $query->whereHas('staff', function ($q) {
                $q->where('warehouse_id', Auth::user()->warehouse_id);
            });
        })->delete();

        $dataArray = $request->all();
        foreach ($dataArray as &$data) {
            $inventory = Inventory::findOrFail($data['inventory_id']);
            $sku_id = $inventory->sku->id;
            $data["sku_id"] = $sku_id;
            $data["status"] = "OPEN";
            unset($data["inventory_id"]);
        }
        unset($data);

        return CycleCountSchedule::insert($dataArray);
    }

    public function index()
    {
        // dd(auth()->user());
        if (Gate::allows('isStaff')) {
            return ScheduleResource::collection(CycleCountSchedule::where('staff_id', Auth::id())->where('status', 'OPEN')->get());
        } else if (Gate::allows('isManager')) {
            $data = CycleCountSchedule::where(function ($query) {
                $query->whereHas('staff', function ($q) {
                    $q->where('warehouse_id', Auth::user()->warehouse_id);
                });
            })->where('status', 'OPEN')->get();
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

    public function reassignStaff(Request $request)
    {
        $request->validate([
            "new_staff" => "required",
            "schedules" => "required"
        ]);
        $warehouse = Warehouse::findOrFail(Auth::user()->warehouse_id);
        CycleCountSchedule::whereIn('id', $request->schedules)->update(["staff_id" => $request->new_staff]);
        $new_staff = User::findOrFail($request->new_staff);
        $newelement = $new_staff["id"] . ":" . $new_staff["name"];

        $new_staffs =
            $warehouse->cycle_counting_settings->staff_ids;
        $ori_settings = $warehouse->cycle_counting_settings;
        array_push($new_staffs, $newelement);
        $ori_settings->staff_ids = $new_staffs;
        $warehouse->cycle_counting_settings = $ori_settings;

        $warehouse->save();
        return;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\CycleCountResource;
use App\Models\CycleCounting;
use App\Models\CycleCountSchedule;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CycleCountController extends Controller
{
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $this->user = Auth::user();

            return $next($request);
        });
    }
    public function index()
    {
        $data = [];
        if (Gate::allows('isStaff')) {
            $data = CycleCounting::where(function ($query) {
                $query->whereHas('schedule', function ($q) {
                    $q->where('staff_id', Auth::id());
                });
            })->get();
        } else if (Gate::allows("isManager")) {
            $data = CycleCounting::where(function ($query) {
                $query->whereHas('schedule', function ($q) {
                    $q->whereHas('staff', function ($r) {
                        $r->where('warehouse_id', Auth::user()->warehouse_id);
                    });
                });
            })->get();
        }
        return CycleCountResource::collection($data);
    }
    /**
     * Show all of the projects for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function show(Request $request)
    {
        // dd(auth()->user());
        $cycle_countings = CycleCounting::where(function ($query) {
            $query->whereHas('schedule', function ($q) {
                $q->where('staff_id', Auth::id());
            });
        })->get();
        // dd($cycle_countings);

        // $this->authorize('view', $cycle_countings);
        return CycleCountResource::collection($cycle_countings);
    }
    public function store(Request $request)
    {

        return CycleCounting::create($request->all());
    }

    public function approveCycleCount(Request $request)
    {
        $cycle_counting = CycleCounting::findOrFail($request->cycle_counting_id);
        $cycle_counting['inv_rec_accuracy'] = $request->ira;
        $cycle_counting['status'] = "COMPLETED";
        $cycle_counting->save();
        $inventory = Inventory::findOrFail($request->inventory_id);
        $inventory->qty_on_hand += $request->variance;
        $inventory->save();
        return;
    }

    public function rejectCycleCount(Request $request)
    {
        $cycle_counting = CycleCounting::findOrFail($request->cycle_counting_id);

        $cycle_counting->status = "REJECTED";
        $cycle_counting->save();
        if ($request->recount) {
            return CycleCountSchedule::create([
                "sku_id" => $cycle_counting->schedule->sku_id,
                "schedule" => $request->schedule_date,
                "staff_id" => $cycle_counting->schedule->staff_id
            ]);
        }
        return;
    }
}

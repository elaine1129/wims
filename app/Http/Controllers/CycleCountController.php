<?php

namespace App\Http\Controllers;

use App\Http\Resources\CycleCountResource;
use App\Models\CycleCounting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function index($warehouseId)
    {
        $data = CycleCounting::where(function ($query) use ($warehouseId) {
            $query->whereHas('schedule', function ($q) use ($warehouseId) {
                $q->whereHas('staff', function ($r) use ($warehouseId) {
                    $r->where('warehouse_id', $warehouseId);
                });
            });
        })->get();
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
}

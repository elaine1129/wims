<?php

namespace App\Http\Controllers;

use App\Http\Resources\CycleCountResource;
use App\Models\CycleCounting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CycleCountController extends Controller
{
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
    public function show()
    {

        // return CycleCountResource::collection(CycleCounting::all());
        return Auth::user();
    }
    public function store(Request $request)
    {

        return CycleCounting::create($request->all());
    }
}

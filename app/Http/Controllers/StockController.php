<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Inventory;
use App\Events\StockCreated;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\StockResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StockController extends Controller
{
    public function index()
    {
        // dd(Auth::user());
        if (Gate::allows('isAdmin')) {
            $stocks = Stock::all();

            return collect(StockResource::collection($stocks))->groupBy('warehouse_id');
        } else if (Gate::allows('isManager')) {
            $stocks = Stock::whereHas('inventory', function ($query) {
                $query->where('warehouse_id', Auth::user()->warehouse_id);
            })->get();
            return StockResource::collection($stocks);
        }
        return abort(403);
    }
    public function store(Request $req)
    {
        $req->validate([
            'quantity' => 'required | gt:0',
            'remarks' => 'required'
        ]);
        $req["staff_id"] = Auth::id();
        if ($req->mode == "checkout") {
            $req->quantity = -abs($req->quantity);
        }
        $inventory =  Inventory::findOrFail($req->inventory_id);
        $inventory->qty_on_hand += $req->quantity;
        $inventory->save();
        event(new StockCreated($inventory));
        return Stock::create($req->all());
    }
}

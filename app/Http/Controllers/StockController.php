<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Inventory;

class StockController extends Controller
{
    public function store(Request $req)
    {
        $req->validate([
            'quantity' => 'required | gt:0',
            'remarks' => 'required'
        ]);
        if ($req->mode == "checkout") {
            $req->quantity = -abs($req->quantity);
        }
        $inventory =  Inventory::findOrFail($req->inventory_id);
        $inventory->qty_on_hand += $req->quantity;
        $inventory->save();
        return Stock::create($req->all());
    }
}

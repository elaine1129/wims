<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function store(Request $req)
    {
        $req->validate([
            'quantity' => 'required | gt:0',
            'remarks' => 'required'
        ]);
        return Stock::create($req->all());
    }
}

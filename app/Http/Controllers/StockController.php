<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function store(Request $req)
    {
        return Stock::create($req->all());
    }
}

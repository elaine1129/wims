<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SKUController extends Controller
{
    public function store(Request $request)
    {
        Sku::where(function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('warehouse_id', Auth::user()->warehouse_id);
            });
        })->delete();
        return Sku::insert($request->all());
    }
}

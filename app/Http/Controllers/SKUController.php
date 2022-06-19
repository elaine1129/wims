<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sku;
use Illuminate\Support\Facades\DB;

class SKUController extends Controller
{
    public function store(Request $request)
    {
        return Sku::insert($request->all());
    }
}

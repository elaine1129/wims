<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getStaffByWarehouse($warehouseId)
    {

        return
            User::where('role', 'staff')
            ->where('warehouse_id', '=', $warehouseId)->get();
    }
}

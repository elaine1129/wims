<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        if (Gate::allows('isAdmin')) {
            return UserResource::collection(User::all());
        } else {
            return User::where('warehouse_id', Auth::user()->warehouse_id)->get();
        }
    }
    public function getOnlyActiveStaffs()
    {
        if (Gate::allows('isAdmin')) {
            return UserResource::collection(User::where(["status" => "ACTIVE", "role" => "Staff"])->get());
        } else {
            return User::where('warehouse_id', Auth::user()->warehouse_id)->where(["status" => "ACTIVE", "role" => "Staff"])->get();
        }
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required | email | unique:App\Models\User,email",
            "contact_no" => "required | min:10 | max:11",
            "ic_no" => ["required", "regex:/^\d{6}\-\d{2}\-\d{4}$/", "unique:App\Models\User,ic_no"],
            "role" => "required",
            "warehouse_id" => "required",
            "employed_in" => "required | date",
            "address" => "required",
            "username" => "required | unique:App\Models\User,username"
        ]);
        $request["password"] = $request->contact_no;
        return User::create($request->all());
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            "email" => ["required", "email",  Rule::unique('users')->ignore($id)],
            "contact_no" => "required | min:10 | max:11",
            "role" => "required",
            "warehouse_id" => "required",
            "address" => "required",
        ]);
        return $user->update($request->all());
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->schedule) {
            return response()->json([
                'errors' => ['The user is holding some cycle count schedule, Please ask manager to reassign the schedules to another staff first. ']
            ], 501);
        }

        $user["status"] = "INACTIVE";
        $user->save();
        return;
        // return User::destroy($id);
    }
    public function getStaffByWarehouse($warehouseId)
    {

        return
            UserResource::collection(User::where('role', 'staff')
                ->where('warehouse_id', '=', $warehouseId)->get());
    }
}

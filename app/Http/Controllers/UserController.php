<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Warehouse;

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
            return UserResource::collection(User::where('warehouse_id', Auth::user()->warehouse_id)->get());
        }
    }
    public function getOnlyActiveStaffs()
    {
        if (Gate::allows('isAdmin')) {
            return UserResource::collection(User::where(["status" => "ACTIVE", "role" => "Staff"])->get());
        } else {
            return UserResource::collection(User::where('warehouse_id', Auth::user()->warehouse_id)->where(["status" => "ACTIVE", "role" => "Staff"])->get());
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
            "contact_no" => "required | min:10 | max:11 | unique:App\Models\User,contact_no",
            "ic_no" => ["required", "regex:/^\d{6}\-\d{2}\-\d{4}$/", "unique:App\Models\User,ic_no"],
            "role" => "required",
            "warehouse_id" => "required",
            "employed_in" => "required | date",
            "address" => "required",
            "username" => "required | unique:App\Models\User,username"
        ]);
        $request["password"] = Hash::make($request->contact_no);
        $user = User::create($request->all());
        if ($request->role == 'Manager') {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);
            $warehouse["manager_id"] =  $user->id;
            $warehouse->save();
        }

        return $user;
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
        //just in case contact number was edited. 
        if ($user["is_first_time_login"] == 1) {
            $user["password"] = Hash::make($request->contact_no);
            $user->save();
        }

        if (($user->role == "Manager" && $request->role == "Staff") || ($request->warehouse_id != $user->warehouse_id)) {
            $warehouse = Warehouse::findOrFail($user->warehouse_id);
            $warehouse["manager_id"] = null;
            $warehouse->save();
        }

        if ($request->role == "Manager") {
            $warehouse = Warehouse::findOrFail($user->warehouse_id);
            $warehouse["manager_id"] = $user->id;
            $warehouse->save();
        }
        return $user->update($request->all());
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->schedule) {
            return response()->json([
                'errors' => ['The user id' . $id . ' is holding some cycle count schedule, Please ask manager to reassign the schedules to another staff first. ']
            ], 501);
        }
        $warehouse = Warehouse::findOrFail($user->warehouse_id);
        $warehouse["manager_id"] = null;
        $warehouse->save();
        $user["role"] = "Staff"; //change back to staff
        $user["status"] = "INACTIVE"; //set inactive so that the data for all reports, stocks still remains for viewing
        $user->save();
        return;
        // return User::destroy($id);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            "new_password" => "required",
            "confirm_password" => "required | same:new_password"
        ]);

        $user = User::findOrFail(Auth::id());
        $user["password"] = Hash::make($request->new_password);
        $user["is_first_time_login"] = 0;
        $user->save();

        return $user;
    }
}

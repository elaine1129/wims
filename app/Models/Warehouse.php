<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;
use App\Models\User;


class Warehouse extends Model
{
    use HasFactory;
    protected $casts = [
        'storage_bins' => 'array',
        'cycle_counting_settings' => 'object'
    ];

    protected $fillable = ["name", "location", "storage_bins"];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function staffs()
    {
        return $this->hasMany(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($warehouse) { // before delete() method call this

            if ($warehouse->inventories) {
                foreach ($warehouse->inventories as $inventory) {
                    $inventory->delete();
                }
            }

            if ($warehouse->staffs) {
                foreach ($warehouse->staffs as $staff) {
                    $staff->delete(); //directly delete not set as INACTIVE cuz the reports, stocks,... no longer important since we're deleting the warehouse
                }
                // $userController = new \App\Http\Controllers\UserController();
                // foreach ($warehouse->staffs as $staff) {
                //     $userController->destroy($staff->id);
                // }
            }

            // do the rest of the cleanup...
        });
    }
}

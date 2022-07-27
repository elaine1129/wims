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
}

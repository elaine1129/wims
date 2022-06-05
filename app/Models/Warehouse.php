<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class Warehouse extends Model
{
    use HasFactory;
    protected $casts = [
        'storage_bins' => 'json',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}

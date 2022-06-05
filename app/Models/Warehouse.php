<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class Warehouse extends Model
{
    use HasFactory;
    protected $casts = [
        'storage_bins' => 'array',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}

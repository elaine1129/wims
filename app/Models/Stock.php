<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ["inventory_id", "quantity", "remarks", "staff_id"];

    public function inventory()
    {
        return $this->belongsTo((Inventory::class));
    }
}

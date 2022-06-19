<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;
use App\Models\CycleCountSchedule;

class Sku extends Model
{
    use HasFactory;
    protected $fillables = ['class', 'inventory_id'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function schedule()
    {
        return $this->hasMany(CycleCountSchedule::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;

class CycleCountSchedule extends Model
{
    use HasFactory;

    protected $fillables = ['sku_id', 'schedule', 'staff_id'];

    public function sku()
    {
        $this->belongsTo(Sku::class);
    }
}

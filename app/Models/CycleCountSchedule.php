<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;
use App\Models\User;
use App\Models\CycleCounting;

class CycleCountSchedule extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['sku_id', 'schedule', 'staff_id'];

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
    public function staff()
    {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }

    public function cycle_counting()
    {
        return $this->hasOne(CycleCounting::class);
    }
}

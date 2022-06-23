<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;
use App\Models\User;


class CycleCountSchedule extends Model
{
    use HasFactory;

    protected $fillables = ['sku_id', 'schedule', 'staff_id'];

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
    public function staff()
    {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }
}

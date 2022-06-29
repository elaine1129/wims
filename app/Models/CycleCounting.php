<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CycleCountSchedule;

class CycleCounting extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', "actual_count", "recorded_count", "variance", "inv_rec_accuracy", "status"];

    public function schedule()
    {
        return $this->belongsTo(CycleCountSchedule::class);
    }
}

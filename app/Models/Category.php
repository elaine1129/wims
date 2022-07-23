<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ["name"];
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}

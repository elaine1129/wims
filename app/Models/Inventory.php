<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Sku;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = ["name", "warehouse_id", "cost_per_unit", "qty_on_hand", "category_id"];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function sku()
    {
        return $this->hasOne(Sku::class);
    }
}

<?php

namespace App\Models;

use App\Http\Controllers\WarehouseController;
use App\Http\Resources\InventoryResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Sku;
use App\Models\CycleCountSchedule;
use App\Models\CycleCounting;

use Illuminate\Support\Arr;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = ["name", "warehouse_id", "cost_per_unit", "qty_on_hand", "category_id", "priority"];

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

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($inventory) { // before delete() method call this
            if ($inventory->category) {
                $warehouse = Warehouse::findOrFail($inventory->warehouse->id);
                $stored_bin = Arr::where((array)$inventory->warehouse->storage_bins, function ($value, $key) use ($inventory) {
                    return $value['inventory_id'] == $inventory->id;
                });

                $bin_id = (array_values($stored_bin))[0]["bin_id"];
                $new_bins = $inventory->warehouse->storage_bins;
                $temp = array_column($new_bins, 'bin_id');
                $found_key = array_search($bin_id, $temp);

                $new_bins[$found_key]["inventory_id"] = -1;

                $warehouse->storage_bins = $new_bins;
                $warehouse->save();
            }

            if ($inventory->sku) {
                CycleCounting::where(function ($query) use ($inventory) {
                    $query->whereHas('schedule', function ($q) use ($inventory) {
                        $q->where('sku_id', $inventory->sku->id);
                    });
                })->delete();
                CycleCountSchedule::where(
                    'sku_id',
                    $inventory->sku->id
                )->delete();
                $inventory->sku()->delete();
            }
            if ($inventory->stocks) {
                $inventory->stocks()->delete();
            }


            // do the rest of the cleanup...
        });
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cost_per_unit' => $this->cost_per_unit,
            'qty_on_hand' => $this->qty_on_hand,
            'warehouse' => $this->warehouse,
            'storage_bin' => Arr::where((array)$this->warehouse->storage_bins, function ($value, $key) {
                return $value['inventory_id'] == $this->id;
            }),
            'category' => $this->category,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by
        ];
    }
}

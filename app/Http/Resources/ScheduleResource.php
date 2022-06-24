<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use App\Http\Resources\SKUResource;
use App\Models\Sku;

class ScheduleResource extends JsonResource
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
            'sku_id' => $this->sku_id,
            'schedule' => $this->schedule,
            'staff' => $this->staff,
            'sku' => new SKUResource($this->sku),
            'storage_bin' => Arr::where($this->sku->inventory->warehouse->storage_bins, function ($value, $key) {
                return $value['inventory_id'] == $this->sku->inventory->id;
            }),

        ];
    }
}

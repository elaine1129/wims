<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class StockResource extends JsonResource
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
            'inventory' => $this->inventory,
            'staff' => $this->staff,
            'warehouse_id' => $this->inventory->warehouse_id,
            'warehouse' => $this->inventory->warehouse,
            'quantity' => $this->quantity,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at
        ];
    }
}

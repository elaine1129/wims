<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $arrayData =  array_merge(parent::toArray($request), [
            // 'manager' => User::findOrFail($this->manager_id),
            'staffs' => $this->staffs
        ]);

        if ($this->manager_id) {
            $arrayData['manager'] =
                User::findOrFail($this->manager_id);
        } else {
            $arrayData['manager'] =
                null;
        }

        return $arrayData;
    }
}

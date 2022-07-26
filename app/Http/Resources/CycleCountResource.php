<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ScheduleResource;

class CycleCountResource extends JsonResource
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
            'schedule' => new ScheduleResource($this->schedule),
            'actual_count' => $this->actual_count,
            'recorded_count' => $this->recorded_count,
            'variance' => $this->variance,
            'inv_rec_accuracy' => $this->inv_rec_accuracy,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'approve_before' => $this->approve_before
        ];
    }
}

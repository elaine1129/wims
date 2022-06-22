<?php

namespace App\Events;

use App\Http\Resources\InventoryResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Inventory;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Http\Resources\Json\JsonResource;

class StockCreated implements ShouldBroadcast
{
    public $inventory;
    use Dispatchable, InteractsWithSockets, SerializesModels;
    use InteractsWithBroadcasting;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
        $this->broadcastVia('pusher');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['check-in-out-stock.' . $this->inventory->warehouse_id];
    }

    public function broadcastWith()
    {
        return [
            'inventory' => $this->inventory

        ];
    }
}

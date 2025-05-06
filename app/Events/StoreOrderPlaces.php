<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreOrderPlaces implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $id;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     * @param int $id
     */
    public function __construct(Order $order, $id)
    {
        $this->order = $order;  
        $this->id = $id;
    }

    /**
     * The channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('StoreOrderPlaces'. $this->id);
    }

    /**
     * The name of the event to broadcast.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'StoreOrderPlaced';  // Unique event name
    }
}

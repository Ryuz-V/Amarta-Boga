<?php

namespace App\Events;

use App\Models\OrderItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemCancelled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrderItem $orderItem;
    public string $reason;

    public function __construct(OrderItem $orderItem, string $reason)
    {
        $this->orderItem = $orderItem->load('order');
        $this->reason = $reason;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('kitchen'),
            new PrivateChannel('pos'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderItemCancelled';
    }

    public function broadcastWith(): array
    {
        return [
            'order_item' => $this->orderItem->toArray(),
            'order_id' => $this->orderItem->order_id,
            'reason' => $this->reason,
            'message' => 'Item ' . $this->orderItem->menu_name . ' dibatalkan. Alasan: ' . $this->reason,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

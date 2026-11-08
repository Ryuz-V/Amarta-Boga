<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $previousStatus;
    public string $newStatus;

    public function __construct(Order $order, string $previousStatus, string $newStatus)
    {
        $this->order = $order->load(['items.modifiers', 'table']);
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('pos'),
            new PrivateChannel('kitchen'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => $this->order->toArray(),
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'message' => 'Status pesanan #' . $this->order->invoice_number . ' diperbarui menjadi: ' . $this->newStatus,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

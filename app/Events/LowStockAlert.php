<?php

namespace App\Events;

use App\Models\Ingredient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ingredient $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'LowStockAlert';
    }

    public function broadcastWith(): array
    {
        return [
            'ingredient_id' => $this->ingredient->id,
            'name' => $this->ingredient->name,
            'sku' => $this->ingredient->sku,
            'current_stock' => $this->ingredient->current_stock,
            'minimum_stock' => $this->ingredient->minimum_stock,
            'unit' => $this->ingredient->unit,
            'message' => 'Peringatan Stok Menipis: ' . $this->ingredient->name . ' sisa ' . $this->ingredient->current_stock . ' ' . $this->ingredient->unit,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

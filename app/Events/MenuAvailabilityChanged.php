<?php

namespace App\Events;

use App\Models\Menu;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuAvailabilityChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Menu $menu;
    public bool $isAvailable;

    public function __construct(Menu $menu, bool $isAvailable)
    {
        $this->menu = $menu;
        $this->isAvailable = $isAvailable;
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
        return 'MenuAvailabilityChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'menu_id' => $this->menu->id,
            'menu_name' => $this->menu->name,
            'is_available' => $this->isAvailable,
            'message' => 'Status menu ' . $this->menu->name . ' diubah menjadi: ' . ($this->isAvailable ? 'Tersedia' : 'Habis (Sold Out)'),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

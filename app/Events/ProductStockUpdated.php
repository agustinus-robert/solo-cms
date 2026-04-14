<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $variantCode;
    public $newStock;
    public $socketId;

    public function __construct($productId, $variantCode, $newStock)
    {
        $this->productId = $productId;
        $this->variantCode = $variantCode;
        $this->newStock = $newStock;
        $this->socketId = request()->header('X-Socket-ID');
    }

    public function broadcastOn()
    {
        return new Channel('products-market');
    }

    public function broadcastAs()
    {
        return 'stock.updated';
    }
}

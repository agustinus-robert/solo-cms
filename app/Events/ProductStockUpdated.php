<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $variantCode;
    public $newStock;

    /**
     * @param int|string $productId
     * @param float|int $newStock
     * @param string|null $variantCode
     */
    public function __construct($productId, $newStock, $variantCode = null)
    {
        $this->productId = $productId;
        $this->newStock = $newStock;
        $this->variantCode = $variantCode;
    }

    public function broadcastOn()
    {
        return new Channel('products-market');
    }

    public function broadcastAs()
    {
        return 'stock.updated';
    }

    public function broadcastWith()
    {
        return [
            'productId'   => (int) $this->productId,
            'variantCode' => (string) $this->variantCode,
            'newStock'    => (int) $this->newStock,
        ];
    }
}

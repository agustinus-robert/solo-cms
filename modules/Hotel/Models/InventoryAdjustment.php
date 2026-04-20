<?php

namespace Modules\Hotel\Models;

use Modules\Account\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $table = 'hotel_inventory_adjustments';
    protected $fillable = ['inventory_id', 'quantity', 'status', 'note', 'user_id'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

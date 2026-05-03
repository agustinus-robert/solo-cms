<?php

namespace Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingOrder extends Model
{
    use SoftDeletes;

    protected $table = 'booking_orders';

    protected $fillable = [
        'order_number', 'tour_package_id', 'customer_name',
        'customer_email', 'customer_phone', 'qty_person',
        'schedule_date', 'total_amount', 'status',
        'payment_gateway', 'payment_channel', 'payment_reference',
        'payment_url', 'payload_data'
    ];

    protected $casts = [
        'payload_data' => 'array',
        'schedule_date' => 'date',
    ];

    public function package()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    public static function generateOrderNumber()
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
    }
}

<?php

namespace Modules\Poz\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleMidtransPayment extends Model
{
    use HasFactory;

    protected $table = 'sale_midtrans_payments';

    protected $fillable = [
        'sale_id',
        'order_id',
        'transaction_id',
        'payment_type',
        'va_number',
        'pdf_url',
        'transaction_status',
        'status_code',
        'gross_amount',
        'full_response',
        'settlement_time',
        'expiry_time',
    ];

    protected $casts = [
        'full_response'   => 'array',
        'gross_amount'    => 'decimal:2',
        'settlement_time' => 'datetime',
        'expiry_time'     => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function isSettlement()
    {
        return $this->transaction_status === 'settlement' || $this->transaction_status === 'capture';
    }
}

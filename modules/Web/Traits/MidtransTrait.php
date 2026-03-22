<?php

namespace Modules\Web\Traits;

use Midtrans\Config;
use Midtrans\CoreApi;

trait MidtransTrait
{
    public function initMidtrans()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function chargeCoreApi($sale, $items, $totals, $bank)
    {
        $this->initMidtrans();

        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $sale->reference,
                'gross_amount' => (int) $totals['grand_total'],
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?? '',
            ],
            'bank_transfer' => [
                'bank' => $bank,
            ],
        ];

        if ($bank == 'mandiri') {
            $params['payment_type'] = 'echannel';
            $params['echannel'] = [
                'bill_info1' => 'Payment For Order',
                'bill_info2' => $sale->reference,
            ];
            unset($params['bank_transfer']);
        }

        return CoreApi::charge($params);
    }
}

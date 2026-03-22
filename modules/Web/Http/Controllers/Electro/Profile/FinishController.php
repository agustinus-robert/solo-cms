<?php

namespace Modules\Web\Http\Controllers\Electro\Profile;

use App\Http\Controllers\Controller;
use Modules\Poz\Models\Sale;
use Modules\Web\Traits\MidtransTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FinishController extends Controller
{
    use MidtransTrait;

    public function index($reference)
    {
        $canEdit = false;
        $sale = Sale::with('midtrans')
                    ->where('reference', $reference)
                    ->firstOrFail();

        try {
            $this->initMidtrans();
            $status = \Midtrans\Transaction::status($sale->reference);

            DB::transaction(function () use ($sale, $status) {
                $midtransStatus = $status->transaction_status;

                if ($midtransStatus == 'settlement' || $midtransStatus == 'capture') {
                    $sale->update(['sale_status' => 2]);
                } elseif (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                    $sale->update(['sale_status' => 3]);
                }

                if ($sale->midtrans) {
                    $sale->midtrans->update([
                        'transaction_status' => $midtransStatus,
                        'full_response' => (array) $status,
                        'settlement_time' => $status->settlement_time ?? $sale->midtrans->settlement_time,
                    ]);
                }
            });

            $sale->refresh();

        } catch (\Exception $e) {
            \Log::warning("Sync Midtrans Error: " . $e->getMessage());
        }

        return view('web::electro.profile.finish', compact('sale', 'canEdit'));
    }
}

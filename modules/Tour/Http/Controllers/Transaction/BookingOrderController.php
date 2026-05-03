<?php

namespace Modules\Tour\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Models\BookingOrder;

class BookingOrderController extends Controller
{
    /**
     * Menangani form submit dari halaman detail tour/package
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id'    => 'required|exists:tour_packages,id',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'qty_person'     => 'required|integer|min:1',
            'schedule_date'  => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => 'Mohon lengkapi data pemesanan dengan benar.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // 2. Hitung Total Harga
            $package = TourPackage::findOrFail($request->package_id);
            $totalAmount = $package->price * $request->qty_person;

            // 3. Simpan ke Database
            $order = BookingOrder::create([
                'order_number'      => BookingOrder::generateOrderNumber(),
                'tour_package_id'   => $package->id,
                'customer_name'     => $request->customer_name,
                'customer_email'    => $request->customer_email,
                'customer_phone'    => $request->customer_phone,
                'qty_person'        => $request->qty_person,
                'schedule_date'     => $request->schedule_date,
                'total_amount'      => $totalAmount,
                'status'            => 'pending', // Status awal
                'payment_gateway'   => config('services.payment.default'), // Ambil dari config/env
            ]);

            // 4. Inisiasi Payment Gateway (Flexible)
            $paymentResult = $this->initiatePayment($order);

            DB::commit();

            // Redirect ke halaman pembayaran atau tampilkan link
            if ($paymentResult['success']) {
                return redirect($paymentResult['payment_url']);
            }

            return redirect()->route('tour::booking.show', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }
    }

    /**
     * Fungsi Pendukung untuk Inisiasi Payment Gateway
     */
    private function initiatePayment(BookingOrder $order)
    {
        $gateway = $order->payment_gateway;

        // Logika fleksibel tergantung gateway yang dipilih
        switch ($gateway) {
            case 'midtrans':
                // Contoh: Panggil fungsi integrasi Midtrans kamu di sini
                // $snapToken = Midtrans::getSnapToken($order);
                $paymentUrl = "#"; // Ganti dengan URL dari Midtrans
                $order->update([
                    'payment_url' => $paymentUrl,
                    'payload_data' => ['token' => 'sample_midtrans_token']
                ]);
                break;

            case 'xendit':
                // Contoh: Panggil fungsi integrasi Xendit kamu di sini
                // $invoice = Xendit::createInvoice($order);
                $paymentUrl = "#"; // Ganti dengan URL dari Xendit
                $order->update([
                    'payment_url' => $paymentUrl,
                    'payload_data' => ['invoice_id' => 'xnd_sample_123']
                ]);
                break;
        }

        return [
            'success' => true,
            'payment_url' => $order->payment_url
        ];
    }

    /**
     * Detail Booking (Halaman setelah checkout atau cek pesanan)
     */
    public function show($order_number)
    {
        $order = BookingOrder::with('package.tour')
            ->where('order_number', $order_number)
            ->firstOrFail();

        return view('tour::booking.show', compact('order'));
    }
}

<?php

namespace Modules\Hotel\Repositories\Booking;

use Modules\Hotel\Models\Booking;
use Modules\Hotel\Models\AdditionalService;
use Illuminate\Support\Facades\DB;

trait ServiceRepositories
{
    /**
     * Menambah layanan ke booking dan mengupdate total tagihan
     */
    public function addServiceToBooking(int $bookingId, array $data)
    {
        return DB::transaction(function () use ($bookingId, $data) {
            $service = AdditionalService::create([
                'booking_id'   => $bookingId,
                'service_name' => $data['service_name'],
                'price'        => $data['price'],
                'quantity'     => $data['quantity'],
                'total'        => $data['price'] * $data['quantity'],
            ]);

            $this->syncBookingTotal($bookingId);

            return $service;
        });
    }

    /**
     * Menghapus layanan dan hitung ulang total
     */
    public function deleteService(int $serviceId)
    {
        return DB::transaction(function () use ($serviceId) {
            $service = AdditionalService::findOrFail($serviceId);
            $bookingId = $service->booking_id;

            $service->delete();
            $this->syncBookingTotal($bookingId);
        });
    }

    /**
     * Helper untuk sinkronisasi total harga booking
     */
    private function syncBookingTotal($bookingId)
    {
        $booking = Booking::with('room.type', 'additionalServices')->findOrFail($bookingId);

        $days = $booking->check_in_plan->diffInDays($booking->check_out_plan) ?: 1;
        $roomCharge = $days * $booking->room->type->base_price;
        $servicesCharge = $booking->additionalServices->sum('total');

        $booking->update([
            'total_price' => $roomCharge + $servicesCharge
        ]);
    }
}

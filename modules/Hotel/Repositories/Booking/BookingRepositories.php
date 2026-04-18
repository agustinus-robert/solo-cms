<?php

namespace Modules\Hotel\Repositories\Booking;

use Modules\Hotel\Models\Booking;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Enums\BookingStatusEnum;
use Modules\Hotel\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait BookingRepositories
{
    /**
     * Mencari kamar berdasarkan tipe yang tidak bentrok dengan booking lain
     */
    public function getAvailableRooms($typeId, $checkInPlan, $checkOutPlan)
    {
        return Room::where('room_type_id', $typeId)
            ->where('status', '!=', RoomStatusEnum::MAINTENANCE)
            ->whereDoesntHave('bookings', function($q) use ($checkInPlan, $checkOutPlan) {
                $q->where('status', '!=', \Modules\Hotel\Enums\BookingStatusEnum::CANCELLED)
                  ->where(function($query) use ($checkInPlan, $checkOutPlan) {
                      $query->where('check_in_plan', '<', $checkOutPlan)
                            ->where('check_out_plan', '>', $checkInPlan);
                  });
            })->get();
    }

    /**
     * Simpan reservasi baru
     */
    public function storeBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            return Booking::create([
                'guest_id'       => $data['guest_id'],
                'room_id'        => $data['room_id'],
                'check_in_plan'  => $data['check_in_plan'],
                'check_out_plan' => $data['check_out_plan'],
                'total_price'    => $data['total_price'],
                'status'         => $data['status'] ?? BookingStatusEnum::PENDING,
                'payment_status' => $data['payment_status'] ?? PaymentStatusEnum::UNPAID,
                'notes'          => $data['notes'] ?? null,
            ]);
        });
    }
}

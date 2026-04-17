<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Models\Booking;
use Modules\Hotel\Models\BookingSource;
use Modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Enums\BookingStatusEnum;
use Modules\Hotel\Enums\PaymentStatusEnum;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Statistik Kamar untuk Cards
        $roomStats = [
            'available'   => Room::where('status', RoomStatusEnum::AVAILABLE)->count(),
            'occupied'    => Room::where('status', RoomStatusEnum::OCCUPIED)->count(),
            'dirty'       => Room::where('status', RoomStatusEnum::DIRTY)->count(),
            'maintenance' => Room::where('status', RoomStatusEnum::MAINTENANCE)->count(),
        ];

        // 2. Data untuk Room Status Chart (Pie Chart)
        // Kita petik labelnya dari Enum yang sudah dibuat sebelumnya
        $room_stats_chart = [
            RoomStatusEnum::AVAILABLE->label()   => $roomStats['available'],
            RoomStatusEnum::OCCUPIED->label()    => $roomStats['occupied'],
            RoomStatusEnum::DIRTY->label()       => $roomStats['dirty'],
            RoomStatusEnum::MAINTENANCE->label() => $roomStats['maintenance'],
        ];

        // 3. Data untuk Booking Source Chart (Doughnut)
        $booking_sources = BookingSource::withCount('bookings')->pluck('bookings_count', 'name')->toArray();

        // 4. Data untuk Payment Status Chart (Pie)
        $payment_stats = [
            'Lunas'         => Booking::where('payment_status', PaymentStatusEnum::PAID)->count(),
            'Belum Bayar'   => Booking::where('payment_status', PaymentStatusEnum::UNPAID)->count(),
            'DP / Sebagian' => Booking::where('payment_status', PaymentStatusEnum::PARTIAL)->count(),
        ];

        // 5. Statistik Ringkas (Arrivals/Departures)
        $stats = [
            'arrivals'   => Booking::whereDate('check_in_plan', $today)->count(),
            'departures' => Booking::whereDate('check_out_plan', $today)->count(),
        ];

        // 6. Daftar Kedatangan Hari Ini
        $todayArrivals = Booking::with(['guest', 'room.type'])
            ->whereDate('check_in_plan', $today)
            ->where('status', BookingStatusEnum::CONFIRMED)
            ->limit(5)
            ->get();

        return view('hotel::dashboard', compact(
            'roomStats',
            'room_stats_chart',
            'booking_sources',
            'payment_stats',
            'stats',
            'todayArrivals'
        ));
    }
}

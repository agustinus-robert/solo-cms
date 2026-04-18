<?php

namespace Modules\Hotel\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\RoomType;
use Modules\Hotel\Models\Guests;
use Modules\Hotel\Models\Booking;
use Modules\Hotel\Http\Requests\Booking\StoreRequest;
use Modules\Hotel\Http\Requests\Booking\UpdateRequest;
use Modules\Hotel\Repositories\Booking\BookingRepositories;
use Modules\Hotel\Enums\BookingStatusEnum;
use Modules\Hotel\Enums\PaymentStatusEnum;
use Modules\Hotel\Enums\RoomStatusEnum;

use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    use BookingRepositories;

    public function index(Request $request)
    {
        $bookings = Booking::with(['guest', 'room.type'])
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return view('hotel::booking._table', compact('bookings'))->render();
        }

        return view('hotel::booking.index', compact('bookings'));
    }

  /**
     * Form untuk reservasi baru.
     */
    public function create()
    {
        $booking = null;
        $roomTypes = RoomType::all();
        $guests = Guests::orderBy('first_name')->get();

        return view('hotel::booking.upsert', compact('booking', 'roomTypes', 'guests'));
    }

    /**
     * Simpan reservasi baru.
     */
    public function store(StoreRequest $request)
    {
        try {
            $this->storeBooking($request->transform());

            return redirect()->route('hotel::booking.index')
                ->with('success', 'Reservasi berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat reservasi: ' . $e->getMessage());
        }
    }

    /**
     * Form edit reservasi.
     */
    public function edit(Booking $booking)
    {
        $roomTypes = RoomType::all();
        $guests = Guests::orderBy('first_name')->get();

        return view('hotel::booking.upsert', compact('booking', 'roomTypes', 'guests'));
    }

    /**
     * Update data reservasi.
     */
    public function update(UpdateRequest $request, Booking $booking)
    {
        try {
            $data = $request->transform();
            $booking->update($data);

            return redirect()->route('hotel::booking.index')
                ->with('success', 'Data reservasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui reservasi: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Cek ketersediaan kamar saat user pilih tanggal & tipe
     */
    public function getAvailable(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $rooms = $this->getAvailableRooms(
            $request->room_type_id,
            $request->check_in,
            $request->check_out
        );

        return response()->json($rooms);
    }

    public function checkin(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            $booking->update([
                'actual_check_in' => now(),
                'status' => BookingStatusEnum::CONFIRMED
            ]);

            $booking->room->update(['status' => RoomStatusEnum::OCCUPIED]);
        });

        return back()->with('success', 'Tamu berhasil Check-in.');
    }

    public function checkout(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            $booking->update([
                'actual_check_out' => now(),
                'status' => BookingStatusEnum::COMPLETED,
                'payment_status' => PaymentStatusEnum::PAID
            ]);

            $booking->room->update(['status' => RoomStatusEnum::AVAILABLE]);
        });

        return back()->with('success', 'Tamu berhasil Check-out.');
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => BookingStatusEnum::CANCELLED]);
        return back()->with('success', 'Reservasi telah dibatalkan.');
    }
}

<?php

namespace Modules\Hotel\Http\Requests\Booking;

use App\Http\Requests\FormRequest;
use Modules\Hotel\Models\Room;
use Carbon\Carbon;

class StoreRequest extends FormRequest
{
    /**
     * Aturan validasi untuk reservasi baru.
     */
    public function rules(): array
    {
        return [
            'guest_id'       => 'required|exists:hotel_guests,id',
            'room_id'        => 'required|exists:hotel_rooms,id',
            'check_in_plan'  => 'required|date|after_or_equal:today',
            'check_out_plan' => 'required|date|after:check_in_plan',
            'payment_status' => 'required', // Enum value
            'notes'          => 'nullable|string|max:500',
        ];
    }

    /**
     * Kustomisasi nama atribut.
     */
    public function attributes(): array
    {
        return [
            'guest_id'       => 'Tamu',
            'room_id'        => 'Kamar',
            'check_in_plan'  => 'Rencana Check-in',
            'check_out_plan' => 'Rencana Check-out',
            'payment_status' => 'Status Pembayaran',
        ];
    }

    /**
     * Transformasi data dan perhitungan harga di sisi server.
     */
    public function transform(): array
    {
        $checkIn  = Carbon::parse($this->input('check_in_plan'));
        $checkOut = Carbon::parse($this->input('check_out_plan'));
        $duration = $checkIn->diffInDays($checkOut);
        if ($duration == 0) $duration = 1; // Minimal 1 hari jika di hari yang sama
        $room = Room::with('type')->find($this->input('room_id'));
        $totalPrice = $duration * ($room->type->base_price ?? 0);

        return [
            'guest_id'       => $this->input('guest_id'),
            'room_id'        => $this->input('room_id'),
            'check_in_plan'  => $checkIn->format('Y-m-d H:i:s'),
            'check_out_plan' => $checkOut->format('Y-m-d H:i:s'),
            'total_price'    => $totalPrice,
            'payment_status' => $this->input('payment_status'),
            'notes'          => $this->input('notes'),
            'status'         => \Modules\Hotel\Enums\BookingStatusEnum::PENDING, // Default status
        ];
    }
}

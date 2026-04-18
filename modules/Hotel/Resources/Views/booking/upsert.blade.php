@extends('hotel::layouts.default')

@section('title', ($booking ? 'Edit' : 'Reservasi Baru') . ' | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <form id="booking-form" action="{{ $booking ? route('hotel::booking.update', $booking->id) : route('hotel::booking.store') }}" method="POST">
            @csrf
            @if($booking) @method('PUT') @endif

            <div class="row">
                {{-- Kiri: Data Tamu & Waktu --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="mdi mdi-calendar-check me-2 text-primary"></i>Informasi Reservasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                {{-- Pilih Tamu --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Pilih Tamu</label>
                                    <select name="guest_id" class="form-select @error('guest_id') is-invalid @enderror" id="guest_id" required>
                                        <option value="">-- Pilih atau Cari Tamu --</option>
                                        @foreach($guests as $guest)
                                            <option value="{{ $guest->id }}" {{ old('guest_id', $booking->guest_id ?? '') == $guest->id ? 'selected' : '' }}>
                                                {{ $guest->id_card_number }} - {{ $guest->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <small class="text-muted">Tamu tidak terdaftar? <a href="{{ route('hotel::guest.create') }}" class="text-primary fw-bold">Registrasi Tamu Baru</a></small>
                                    </div>
                                </div>

                                {{-- Rentang Waktu --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Rencana Check-in</label>
                                    <input type="date" name="check_in_plan" id="check_in_plan"
                                        class="form-control @error('check_in_plan') is-invalid @enderror"
                                        value="{{ old('check_in_plan', isset($booking) && $booking->check_in_plan ? $booking->check_in_plan->format('Y-m-d') : date('Y-m-d')) }}"
                                        required>
                                </div>

                                {{-- Rencana Check-out --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Rencana Check-out</label>
                                    <input type="date" name="check_out_plan" id="check_out_plan"
                                        class="form-control @error('check_out_plan') is-invalid @enderror"
                                        value="{{ old('check_out_plan', isset($booking) && $booking->check_out_plan ? $booking->check_out_plan->format('Y-m-d') : date('Y-m-d', strtotime('+1 day'))) }}"
                                        required>
                                </div>

                                {{-- Pilih Tipe & Kamar --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tipe Kamar</label>
                                    <select id="room_type_id" class="form-select">
                                        <option value="">-- Pilih Tipe --</option>
                                        @foreach($roomTypes as $type)
                                            <option value="{{ $type->id }}" data-price="{{ $type->base_price }}">
                                                {{ $type->name }} (Rp {{ number_format($type->base_price, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pilih Nomor Kamar</label>
                                    <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required disabled>
                                        <option value="">-- Pilih Tipe & Tanggal Dulu --</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Catatan Tambahan</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Misal: Minta high floor, extra towel, dll">{{ old('notes', $booking->notes ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Ringkasan Harga --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0 fw-bold">Ringkasan Biaya</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Harga /Malam</span>
                                <span id="label-price" class="fw-bold">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Durasi Inap</span>
                                <span id="label-duration" class="fw-bold">0 Malam</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5">Total</span>
                                <span id="label-total" class="h5 fw-bold text-primary">Rp 0</span>
                            </div>

                            <input type="hidden" name="total_price" id="total_price" value="0">

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Status Pembayaran</label>
                                <select name="payment_status" class="form-select form-select-sm">
                                    @foreach(\Modules\Hotel\Enums\PaymentStatusEnum::cases() as $payment)
                                        <option value="{{ $payment->value }}">{{ $payment->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="mdi mdi-check-circle me-1"></i> Konfirmasi Reservasi
                            </button>
                            <a href="{{ route('hotel::booking.index') }}" class="btn btn-light w-100 mt-2">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in_plan');
    const checkOutInput = document.getElementById('check_out_plan');
    const typeSelect = document.getElementById('room_type_id');
    const roomSelect = document.getElementById('room_id');
    const labelPrice = document.getElementById('label-price');
    const labelDuration = document.getElementById('label-duration');
    const labelTotal = document.getElementById('label-total');
    const hiddenTotal = document.getElementById('total_price');

    async function updateAvailability() {
        const typeId = typeSelect.value;
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;

        if(!typeId || !checkIn || !checkOut) return;

        roomSelect.disabled = true;
        roomSelect.innerHTML = '<option>Mencari kamar...</option>';

        try {
            const response = await fetch(`{{ route('hotel::room.available') }}?room_type_id=${typeId}&check_in=${checkIn}&check_out=${checkOut}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const rooms = await response.json();

            roomSelect.innerHTML = rooms.length > 0
                ? '<option value="">-- Pilih Kamar --</option>'
                : '<option value="">Kamar Penuh / Tidak Tersedia</option>';

            rooms.forEach(room => {
                roomSelect.innerHTML += `<option value="${room.id}">Kamar ${room.room_number} (Lantai ${room.floor})</option>`;
            });

            roomSelect.disabled = rooms.length === 0;
            calculateTotal();
        } catch (e) {
            console.error(e);
        }
    }

    function calculateTotal() {
        const price = typeSelect.options[typeSelect.selectedIndex]?.dataset.price || 0;
        const start = new Date(checkInInput.value);
        const end = new Date(checkOutInput.value);

        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        const total = diffDays * price;

        labelPrice.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(price)}`;
        labelDuration.innerText = `${diffDays} Malam`;
        labelTotal.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        hiddenTotal.value = total;
    }

    [checkInInput, checkOutInput, typeSelect].forEach(el => {
        el.addEventListener('change', updateAvailability);
    });
});
</script>
@endpush

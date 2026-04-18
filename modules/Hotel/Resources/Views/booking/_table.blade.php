<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted small uppercase">
            <tr>
                <th class="ps-4">Tamu & Kamar</th>
                <th>Rencana Menginap</th>
                <th>Aktual</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th class="text-end pe-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $booking->guest->full_name }}</div>
                        <div class="small">
                            <span class="badge bg-soft-secondary text-secondary">
                                Kamar: {{ $booking->room->room_number }} ({{ $booking->room->type->name }})
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="small">
                            <i class="mdi mdi-calendar-import text-success me-1"></i> {{ $booking->check_in_plan->format('d M Y') }}
                        </div>
                        <div class="small">
                            <i class="mdi mdi-calendar-export text-danger me-1"></i> {{ $booking->check_out_plan->format('d M Y') }}
                        </div>
                    </td>
                    <td>
                        @if($booking->actual_check_in)
                            <div class="small text-muted italic">In: {{ $booking->actual_check_in->format('H:i') }}</div>
                            @if($booking->actual_check_out)
                                <div class="small text-muted italic">Out: {{ $booking->actual_check_out->format('H:i') }}</div>
                            @else
                                <span class="badge bg-soft-warning text-warning small">In-House</span>
                            @endif
                        @else
                            <span class="text-muted small">- Belum Check-in -</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-dark">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
                        <small class="badge {{ $booking->payment_status->name == 'PAID' ? 'bg-success' : 'bg-danger' }} rounded-pill" style="font-size: 0.7rem">
                            {{ $booking->payment_status->name }}
                        </small>
                    </td>
                    <td>
                        {{-- Menggunakan label dari Enum BookingStatus --}}
                        <span class="badge border {{ $booking->status->color() ?? 'border-secondary text-secondary' }}">
                            {{ $booking->status->name }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                       <div class="dropdown">
                            <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="{{ route('hotel::booking.edit', $booking->id) }}"><i class="mdi mdi-eye me-2"></i>Detail / Edit</a></li>

                                @if(!$booking->actual_check_in && $booking->status->name !== 'CANCELLED')
                                    <li>
                                        <a class="dropdown-item text-primary" href="javascript:void(0)" onclick="confirmAction('{{ route('hotel::booking.checkin', $booking->id) }}', 'Proses Check-in tamu sekarang?')">
                                            <i class="mdi mdi-login me-2"></i>Check-in
                                        </a>
                                    </li>
                                @elseif($booking->actual_check_in && !$booking->actual_check_out)
                                    <li>
                                        <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmAction('{{ route('hotel::booking.checkout', $booking->id) }}', 'Proses Check-out tamu sekarang?')">
                                            <i class="mdi mdi-logout me-2"></i>Check-out
                                        </a>
                                    </li>
                                @endif

                                @if($booking->status->name !== 'CANCELLED' && !$booking->actual_check_out)
                                    <div class="dropdown-divider"></div>
                                    <li>
                                        <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmAction('{{ route('hotel::booking.cancel', $booking->id) }}', 'Batalkan reservasi ini?')">
                                            <i class="mdi mdi-close-circle me-2"></i>Batalkan
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        {{-- Form Tersembunyi untuk Aksi --}}
                        <form id="action-form" method="POST" style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada data reservasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($bookings->hasPages())
<div class="card-footer bg-white border-top-0 pt-0 pb-3">
    {{ $bookings->links() }}
</div>
@endif

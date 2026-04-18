@extends('hotel::layouts.default')

@section('title', 'Detail Reservasi ' . $booking->booking_code . ' | ')

@section('content')
<div class="row">
    {{-- Header Info --}}
    <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0">Reservasi #{{ $booking->id }}</h4>
            <span class="badge {{ $booking->status->color() }}">{{ $booking->status->name }}</span>
            <span class="badge {{ $booking->payment_status->name == 'PAID' ? 'bg-success' : 'bg-danger' }}">
                {{ $booking->payment_status->name }}
            </span>
        </div>
        <div class="btn-group">
            <a href="{{ route('hotel::booking.index') }}" class="btn btn-light border"> Kembali</a>
            <button class="btn btn-primary" onclick="window.print()"><i class="mdi mdi-printer"></i> Cetak Invoice</button>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Card Tamu & Kamar --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Informasi Tamu & Kamar</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nama Tamu:</small>
                    <span class="fw-bold text-dark h5">{{ $booking->guest->full_name }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Identitas (NIK):</small>
                    <span class="text-dark">{{ $booking->guest->id_card_number }}</span>
                </div>
                <hr>
                <div class="mb-3">
                    <small class="text-muted d-block">Kamar:</small>
                    <span class="fw-bold text-primary h6">{{ $booking->room->room_number }} ({{ $booking->room->type->name }})</span>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted d-block">Check-in Plan:</small>
                        <span class="small">{{ $booking->check_in_plan->format('d M Y') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Check-out Plan:</small>
                        <span class="small">{{ $booking->check_out_plan->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Card Billing / Services --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Rincian Biaya & Layanan</h6>
                @if($booking->status->name != 'CANCELLED' && $booking->status->name != 'COMPLETED')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="mdi mdi-plus"></i> Tambah Layanan
                </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-4">Item/Layanan</th>
                                <th class="text-center">Qty/Malam</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Baris Biaya Kamar --}}
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">Sewa Kamar ({{ $booking->room->type->name }})</div>
                                    <small class="text-muted">{{ $booking->check_in_plan->format('d M') }} - {{ $booking->check_out_plan->format('d M') }}</small>
                                </td>
                                <td class="text-center">{{ $booking->check_in_plan->diffInDays($booking->check_out_plan) ?: 1 }}</td>
                                <td class="text-end">Rp{{ number_format($booking->room->type->base_price, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-bold">
                                    Rp{{ number_format(($booking->check_in_plan->diffInDays($booking->check_out_plan) ?: 1) * $booking->room->type->base_price, 0, ',', '.') }}
                                </td>
                            </tr>
                            {{-- Layanan Tambahan --}}
                            @foreach($booking->additionalServices as $service)
                            <tr>
                                <td class="ps-4">
                                    <div>{{ $service->service_name }}</div>
                                    <small class="text-danger" style="cursor:pointer" onclick="deleteService({{ $service->id }})">Hapus</small>
                                </td>
                                <td class="text-center">{{ $service->quantity }}</td>
                                <td class="text-end">Rp{{ number_format($service->price, 0, ',', '.') }}</td>
                                <td class="text-end pe-4">Rp{{ number_format($service->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr class="h5">
                                <td colspan="3" class="text-end py-3">Grand Total:</td>
                                <td class="text-end pe-4 py-3 text-primary fw-bold">
                                    Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Layanan --}}
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('hotel::services.store') }}" method="POST">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Layanan Tambahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Layanan</label>
                        <input type="text" name="service_name" class="form-control" placeholder="Contoh: Laundry, Extra Bed, Makan Siang" required>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Harga Satuan</label>
                            <input type="number" name="price" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form Hapus Service (Hidden) --}}
<form id="delete-service-form" action="" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<script>
    function deleteService(id) {
        if(confirm('Hapus layanan ini?')) {
            const form = document.getElementById('delete-service-form');
            form.action = `/hotel/services/${id}`;
            form.submit();
        }
    }
</script>
@endsection

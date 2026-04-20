@extends('hotel::layouts.default')

@section('title', 'Set Inventaris Kamar ' . $room->room_number . ' | ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('hotel::room.index') }}" class="btn btn-light border me-3">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0">Set Inventaris: Kamar {{ $room->room_number }}</h4>
                    <p class="text-muted small mb-0">Atur barang standar yang tersedia di kamar ini.</p>
                </div>
            </div>
            {{-- Info Ringkas --}}
            <span class="badge bg-soft-info text-info p-2 px-3">
                <i class="mdi mdi-office-building-marker me-1"></i> Lantai {{ $room->floor }}
            </span>
        </div>

        <form action="{{ route('hotel::room-inventory.update', $room->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @include('hotel::room-inventory._table')
                </div>
                <div class="card-footer bg-white py-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('hotel::room.index') }}" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-check-all me-1"></i> Simpan Inventaris Kamar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('Script Inventaris Ready, Robert!'); // Cek ini muncul gak di Console?

    const checkboxes = document.querySelectorAll('.inventory-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.inventory-qty');
            const noteInput = row.querySelector('.inventory-note');

            console.log('Switch ID ' + this.value + ' is: ' + (this.checked ? 'ON' : 'OFF'));

            if (this.checked) {
                // Hapus atribut disabled
                qtyInput.removeAttribute('disabled');
                noteInput.removeAttribute('disabled');
                qtyInput.focus();
            } else {
                // Pasang lagi atribut disabled
                qtyInput.setAttribute('disabled', 'disabled');
                noteInput.setAttribute('disabled', 'disabled');
            }
        });
    });
});
</script>

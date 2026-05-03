@extends('tour::layouts.default')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ $label ? route('tour::label.update', ['label' => $label->id]) : route('tour::label.store') }}"
              method="POST">
            @csrf
            @if($label) @method('PUT') @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">{{ $label ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru' }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Label/Fasilitas</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $label->name ?? '') }}" placeholder="Contoh: Free WiFi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Icon (MDI)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light" id="preview-icon">
                                    <i class="mdi {{ old('icon', $label->icon ?? 'mdi-check') }}"></i>
                                </span>
                                <input type="text" name="icon" id="icon-input" class="form-control"
                                       value="{{ old('icon', $label->icon ?? 'mdi-check') }}" placeholder="mdi-wifi" readonly>
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#iconModal">
                                    Pilih
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Warna Label</label>
                            <input type="color" name="color" class="form-control form-control-color w-100" value="{{ old('color', $label->color ?? '#556ee6') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end">
                    <a href="{{ route('tour::label.index') }}" class="btn btn-light border me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="iconModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pilih Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="search-icon" class="form-control" placeholder="Cari icon (misal: wifi, food, car)..." onkeyup="filterIcons()">
                </div>

                <div class="row g-2 overflow-auto" style="max-height: 350px;" id="icon-list">
                    @php
                        $icons = [
                            'mdi-tag', 'mdi-wifi', 'mdi-food', 'mdi-coffee', 'mdi-bed', 'mdi-car', 'mdi-bus', 'mdi-airplane',
                            'mdi-camera', 'mdi-map-marker', 'mdi-ticket', 'mdi-account-group', 'mdi-swim', 'mdi-beach',
                            'mdi-image-filter-hdr', 'mdi-tree', 'mdi-translate', 'mdi-shield-check', 'mdi-clock-outline',
                            'mdi-wallet', 'mdi-star', 'mdi-heart', 'mdi-flash', 'mdi-umbrella', 'mdi-image', 'mdi-video',
                            'mdi-music', 'mdi-phone', 'mdi-email', 'mdi-briefcase', 'mdi-store', 'mdi-shopping', 'mdi-gift'
                        ];
                    @endphp
                    @foreach($icons as $icon)
                    <div class="col-3 col-md-2 text-center icon-item">
                        <button type="button" class="btn btn-outline-light text-dark w-100 py-3 border-0" onclick="selectIcon('{{ $icon }}')" data-bs-dismiss="modal">
                            <i class="mdi {{ $icon }} fs-2 d-block mb-1"></i>
                            <small class="d-block text-truncate" style="font-size: 10px;">{{ str_replace('mdi-', '', $icon) }}</small>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Set icon terpilih ke input dan preview
     */
    function selectIcon(iconName) {
        document.getElementById('icon-input').value = iconName;
        document.getElementById('preview-icon').innerHTML = `<i class="mdi ${iconName}"></i>`;
    }

    /**
     * Filter icon berdasarkan input search
     */
    function filterIcons() {
        const input = document.getElementById('search-icon').value.toLowerCase();
        const items = document.querySelectorAll('.icon-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(input) ? "" : "none";
        });
    }

    // Reset search saat modal ditutup
    document.getElementById('iconModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('search-icon').value = '';
        filterIcons();
    });
</script>
@endpush

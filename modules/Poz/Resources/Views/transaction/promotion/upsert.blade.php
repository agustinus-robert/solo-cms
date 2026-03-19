<div class="row mb-4 align-items-center">
    <div class="col-12 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1">Manajemen Promo</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Produk</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ ucfirst($action) }} Promo</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('poz::transaction.product-promotion.index') }}?outlet={{ $outlet_id }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<form method="POST"
      action="{{ isset($promotion) ? route('poz::transaction.product-promotion.update', $promotion->id) : route('poz::transaction.product-promotion.store') }}"
      enctype="multipart/form-data">

    @csrf
    @if(isset($promotion)) @method('PUT') @endif

    <input type="hidden" name="outlet_id" value="{{ $outlet_id }}">

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Informasi Dasar</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Promo</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Contoh: Promo Laptop" value="{{ $promotion->name ?? '' }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tipe Promo</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Tipe --</option>
                                <option value="1" {{ (isset($promotion) && $promotion->type->value == 1) ? 'selected' : '' }}>Per Produk</option>
                                <option value="2" {{ (isset($promotion) && $promotion->type->value == 2) ? 'selected' : '' }}>Bundle (Grosir/Kategori)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Gambar Promo</label>
                            <input type="file" name="image" class="form-control">
                            @if(isset($promotion) && $promotion->image_name)
                                <small class="text-muted d-block mt-1">File: {{ $promotion->image_name }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ isset($promotion) ? $promotion->start_date->format('Y-m-d') : date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ isset($promotion) && $promotion->end_date ? $promotion->end_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            @include('poz::transaction.promotion.partials.products-promo')
            @include('poz::transaction.promotion.partials.products-bundle')

            {{-- Placeholder jika belum pilih tipe --}}
            <div id="placeholder-config" class="card border-0 shadow-sm mb-4 bg-light text-center py-5">
                <div class="card-body">
                    <i class="bi bi-gear-wide-connected fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Silakan pilih <b>Tipe Promo</b> terlebih dahulu untuk mengatur konfigurasi.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2 mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body d-flex align-items-center justify-content-between py-3">
                    <div class="d-none d-md-flex align-items-center">
                        <i class="bi bi-info-circle me-3 fs-4"></i>
                        <span>Pastikan kriteria produk dan nilai reward sudah sesuai sebelum menyimpan data ini ke sistem.</span>
                    </div>
                    <button type="submit" class="btn btn-light btn-lg px-5 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-2"></i> Simpan Data Promo
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const productSection = document.getElementById('config-product');
    const bundleSection = document.getElementById('config-bundle');
    const placeholderConfig = document.getElementById('placeholder-config');

    function toggleMainConfig() {
        const val = typeSelect.value;

        // Sembunyikan semua dulu
        if(productSection) productSection.style.display = 'none';
        if(bundleSection) bundleSection.style.display = 'none';
        if(placeholderConfig) placeholderConfig.style.display = 'none';

        if (val == "1") {
            if(productSection) productSection.style.display = 'block';
        } else if (val == "2") {
            if(bundleSection) bundleSection.style.display = 'block';
        } else {
            if(placeholderConfig) placeholderConfig.style.display = 'block';
        }
    }

    typeSelect.addEventListener('change', toggleMainConfig);
    toggleMainConfig(); // Run on load
});
</script>

<style>
    .card { border-radius: 15px; }
    .card-header { border-bottom: 1px solid #f0f0f0; }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13,110,253,.08); }
</style>

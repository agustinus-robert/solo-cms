<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between mb-3">
                <h4 class="font-size-18 mb-0 text-primary fw-bold">Supplier</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="javascript: void(0);" class="text-muted">Supplier</a></li>
                        <li class="breadcrumb-item active text-dark fw-semibold">{{ $action }} Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-person-badge me-2 text-primary"></i>{{ $action }} Data Supplier
            </h5>
        </div>

        <form wire:submit="save" enctype="multipart/form-data">
            <div class="card-body p-4">
                <div class="row g-4">
                    {{-- SISI KIRI: INFORMASI UTAMA --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Code</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-light"><i class="bi bi-hash"></i></span>
                                <input disabled wire:model="form.code" type="text" class="form-control border-light bg-light shadow-none">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Nama Supplier</label>
                            <input type="text" class="form-control shadow-none @error('form.name') is-invalid @enderror"
                                   wire:model="form.name" placeholder="Masukkan nama supplier...">
                            @error('form.name')
                                <div class="text-danger small mt-2"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Email</label>
                                <input type="email" class="form-control shadow-none @error('form.email') is-invalid @enderror"
                                       wire:model="form.email" placeholder="example@mail.com">
                                @error('form.email')
                                    <div class="text-danger small mt-2"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Nomor Telepon</label>
                                <input type="text" class="form-control shadow-none @error('form.phone') is-invalid @enderror"
                                       wire:model="form.phone" placeholder="0812xxxx">
                                @error('form.phone')
                                    <div class="text-danger small mt-2"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN: ALAMAT & FILE --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Alamat Lengkap</label>
                            <textarea class="form-control shadow-none @error('form.address') is-invalid @enderror"
                                      wire:model="form.address" rows="4" placeholder="Jl. Nama Jalan No. XX..."></textarea>
                            @error('form.address')
                                <div class="text-danger small mt-2"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Dokumen Pendukung</label>
                            <input type="file" class="form-control shadow-none" wire:model="document">
                            @if (!empty($form['document']) && isset($form['id']))
                                <div class="mt-2 p-2 bg-light rounded-2 border d-inline-block">
                                    <i class="bi bi-file-earmark-arrow-down text-primary"></i>
                                    <a href="{{ asset($form['document']) }}" class="text-decoration-none small fw-bold">Download Dokumen Saat Ini</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BAGIAN FULL WIDTH: PENGATURAN JADWAL --}}
                    <div class="col-12">
                        <div class="p-3 rounded-3 border-start border-4 border-primary bg-light shadow-sm mt-2">
                            <label class="form-label d-block fw-bold text-dark mb-3">
                                <i class="bi bi-gear-fill me-2"></i>Pengaturan Jadwal Stok
                            </label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check custom-option border p-3 rounded-3 bg-white flex-grow-1 cursor-pointer">
                                    <input class="form-check-input ms-0 me-2" type="radio"
                                        wire:model="form.is_schedule" value="true" id="is_schedule_true">
                                    <label class="form-check-label text-primary fw-bold cursor-pointer" for="is_schedule_true">
                                        <i class="bi bi-calendar-check me-1"></i> Wajib Sesuai Jadwal
                                    </label>
                                    <p class="small text-muted mb-0 mt-2 ms-4">Produk hanya muncul saat Adjustment jika ada jadwal pengiriman hari ini.</p>
                                </div>

                                <div class="form-check custom-option border p-3 rounded-3 bg-white flex-grow-1 cursor-pointer">
                                    <input class="form-check-input ms-0 me-2" type="radio"
                                        wire:model="form.is_schedule" value="false" id="is_schedule_false">
                                    <label class="form-check-label text-secondary fw-bold cursor-pointer" for="is_schedule_false">
                                        <i class="bi bi-list-ul me-1"></i> Bebas (Tanpa Jadwal)
                                    </label>
                                    <p class="small text-muted mb-0 mt-2 ms-4">Semua produk supplier ini bebas di-adjust kapan saja tanpa jadwal.</p>
                                </div>
                            </div>
                            @error('form.is_schedule')
                                <div class="text-danger small mt-2"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-transparent border-top p-4 text-end">
                <button type="button" class="btn btn-light px-4 me-2">Batal</button>
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i> Simpan Data Supplier
                </button>
            </div>
        </form>
    </div>

    <style>
        .cursor-pointer { cursor: pointer; }
        .custom-option {
            transition: all 0.2s ease;
            min-width: 250px;
        }
        .custom-option:hover {
            border-color: #0d6efd !important;
            background-color: #f8f9ff !important;
        }
        .form-check-input:checked + .form-check-label {
            color: #0d6efd !important;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
        }
    </style>
</div>

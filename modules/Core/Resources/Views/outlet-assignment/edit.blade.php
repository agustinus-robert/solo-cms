@extends('core::layouts.default')

@section('title', 'Kelola Petugas Outlet | ')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Halaman --}}
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold text-dark mb-1">Penempatan Petugas Outlet</h4>
            <p class="text-muted small mb-0">Kelola siapa saja kasir/petugas yang aktif di outlet ini.</p>
        </div>
        <a href="{{ route('core::manage-outlet.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('core::manage-outlet.update', $outlet->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="avatar-xl bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="mdi mdi-store" style="font-size: 40px;"></i>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $outlet->name }}</h5>
                            <span class="badge bg-light border text-primary px-3 rounded-pill">{{ $outlet->code }}</span>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-4">
                            <div class="d-flex mb-2 small">
                                <span class="text-muted me-2" style="min-width: 70px;">Lokasi:</span>
                                <span class="fw-bold text-dark">{{ $outlet->location ?? 'Tidak ada lokasi' }}</span>
                            </div>
                            <div class="d-flex small">
                                <span class="text-muted me-2" style="min-width: 70px;">Admin:</span>
                                <span class="fw-bold text-dark">{{ $outlet->admin_id ?? '-' }}</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 shadow fw-bold">
                            <i class="mdi mdi-content-save-check-outline me-2"></i> Simpan Penempatan
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">Daftar User (Role: Outlet / Casier)</h6>
                        <span class="badge bg-soft-info text-info rounded-pill px-3">{{ $users->count() }} Tersedia</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            @forelse($users as $user)
                                <div class="col-md-6">
                                    <label class="d-block cursor-pointer mb-0">
                                        <div class="p-3 border rounded-4 transition-all hover-shadow d-flex align-items-center"
                                             style="cursor: pointer; border-width: 2px !important;">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input me-3" type="checkbox"
                                                       name="user_ids[]" value="{{ $user->id }}"
                                                       id="userCheck{{ $user->id }}"
                                                       {{ $outlet->users->contains($user->id) ? 'checked' : '' }}
                                                       style="width: 22px; height: 22px;">
                                            </div>
                                            <div class="ms-1">
                                                <span class="fw-bold d-block text-dark small">{{ strtoupper($user->name) }}</span>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted opacity-50 mb-3">
                                        <i class="mdi mdi-account-off-outline" style="font-size: 60px;"></i>
                                    </div>
                                    <p class="text-muted">Tidak ada user dengan role petugas/casier yang ditemukan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .hover-shadow:hover {
        background-color: #f8fbff;
        border-color: #3b7ddd !important;
    }

    input.form-check-input:checked + label div {
        border-color: #3b7ddd !important;
        background-color: #f0f7ff;
    }
</style>
@endsection

@extends('acc::layouts.default')

@section('title', ($period ? 'Edit' : 'Tambah') . ' Periode | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="mdi {{ $period ? 'mdi-pencil' : 'mdi-plus' }} me-2"></i>
                    {{ $period ? 'Edit Periode' : 'Tambah Periode Baru' }}
                </h5>
            </div>
            <form action="{{ $period ? route('acc::period.update', $period->id) : route('acc::period.store') }}" method="POST">
                @csrf
                @if($period) @method('PUT') @endif

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $period->name ?? '') }}" placeholder="Contoh: Januari 2026" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', $period->start_date ?? '') }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', $period->end_date ?? '') }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if($period)
                    <div class="form-check form-switch mt-4">
                        <input type="hidden" name="is_closed" value="0">
                        <input class="form-check-input" type="checkbox" name="is_closed" id="is_closed" value="1"
                               {{ old('is_closed', $period->is_closed) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_closed">Tutup Buku (Closed)</label>
                        <div class="form-text text-muted">Periode yang ditutup tidak dapat menerima input jurnal baru.</div>
                    </div>
                    @endif
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('acc::period.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

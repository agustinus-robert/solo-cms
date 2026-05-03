@extends('tour::layouts.default')

@section('title', ($location ? 'Edit' : 'Tambah') . ' Lokasi | ')

@section('navtitle', ($location ? 'Edit' : 'Tambah') . ' Lokasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $location ? route('tour::location.update', $location->id) : route('tour::location.store') }}" method="POST">
                    @csrf
                    @if($location)
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Lokasi / Daerah</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $location->name ?? '') }}"
                               placeholder="Masukkan nama lokasi..." autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tour::location.index') }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-content-save me-1"></i> {{ $location ? 'Simpan Perubahan' : 'Simpan Lokasi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('core::layouts.default')

@section('title', 'Kelola Tipe Posisi | ')
@section('navtitle', 'Kelola tipe posisi')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-10">
            <div class="d-flex align-items-center mb-4">
                <a class="text-decoration-none" href="{{ request('next', route('core::company.position-type.index')) }}">
                    <i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i>
                </a>
                <div class="ms-4">
                    <h2 class="mb-1">{{ $item->id ? 'Edit tipe posisi' : 'Buat tipe posisi baru' }}</h2>
                    <div class="text-secondary small">{{ $title }}</div>
                </div>
            </div>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <form class="form-block" action="{{ $item->id ? route('core::company.position-type.update', $item->id) : route('core::company.position-type.store', ['next' => request('next')]) }}" method="POST">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif

                        <div class="row required mb-3">
                            <label class="col-lg-4 col-xl-3 col-form-label">Kode tipe posisi</label>
                            <div class="col-xl-8 col-xxl-4">
                                <input type="text" class="form-control @error('kd') is-invalid @enderror" name="kd"
                                    value="{{ old('kd', $item->kd) }}"
                                    placeholder="Contoh: MGR, STF"
                                    {{ $item->id ? 'readonly' : 'required' }} />
                                @error('kd')
                                    <small class="text-danger d-block"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>

                        <div class="row required mb-3">
                            <label class="col-lg-4 col-xl-3 col-form-label">Nama tipe posisi</label>
                            <div class="col-xl-8 col-xxl-6">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name', $item->name) }}" required />
                                @error('name')
                                    <small class="text-danger d-block"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>

                        <div class="required row mb-3">
                            <label class="col-lg-4 col-xl-3 col-form-label">Status Aktif</label>
                            <div class="col-lg-8">
                                <div class="btn-group">
                                    <input class="btn-check" type="radio" id="is_active1" name="is_active" value="1"
                                        autocomplete="off" @checked(old('is_active', $item->is_active) == 1) required />
                                    <label class="btn btn-outline-light text-dark" for="is_active1">
                                        <i class="mdi mdi-check-circle-outline"></i> Aktif
                                    </label>

                                    <input class="btn-check" type="radio" id="is_active0" name="is_active" value="0"
                                        autocomplete="off" @checked(old('is_active', $item->is_active) === 0 || old('is_active', $item->is_active) === "0") required />
                                    <label class="btn btn-outline-light text-dark" for="is_active0">
                                        <i class="mdi mdi-close-circle-outline"></i> Non-Aktif
                                    </label>
                                </div>
                                @error('is_active')
                                    <small class="text-danger d-block"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-xl-3 col-form-label">Keterangan</label>
                            <div class="col-lg-8">
                                <textarea class="form-control @error('meta.description') is-invalid @enderror" name="meta[description]" rows="3">{{ old('meta.description', $item->meta['description'] ?? '') }}</textarea>
                                <small class="text-secondary small italic">Informasi tambahan untuk tipe posisi ini.</small>
                                @error('meta.description')
                                    <small class="text-danger d-block"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-8 offset-lg-4 offset-xl-3 pt-3 border-top">
                                <button class="btn btn-soft-danger px-4">
                                    <i class="mdi mdi-check"></i> {{ $item->id ? 'Perbarui' : 'Simpan' }}
                                </button>
                                <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('core::company.position-type.index')) }}">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

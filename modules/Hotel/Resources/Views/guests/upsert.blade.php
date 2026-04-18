@extends('hotel::layouts.default')

@section('title', ($guest ? 'Edit' : 'Registrasi') . ' Tamu | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="mdi {{ $guest ? 'mdi-account-edit' : 'mdi-account-plus' }} me-2 text-primary"></i>
                    {{ $guest ? 'Edit Data Tamu: ' . $guest->full_name : 'Registrasi Tamu Baru' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ $guest ? route('hotel::guest.update', $guest->id) : route('hotel::guest.store') }}" method="POST">
                    @csrf
                    @if($guest)
                        @method('PUT')
                    @endif

                    <div class="row g-4">
                        {{-- ID Card / NIK --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nomor Identitas (NIK / Passport)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="mdi mdi-id-card"></i></span>
                                <input type="text" name="id_card_number"
                                    class="form-control @error('id_card_number') is-invalid @enderror"
                                    value="{{ old('id_card_number', $guest->id_card_number ?? '') }}"
                                    placeholder="Masukkan 16 digit NIK" required>
                            </div>
                            @error('id_card_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Depan --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Depan</label>
                            <input type="text" name="first_name"
                                class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name', $guest->first_name ?? '') }}"
                                placeholder="Contoh: Robert" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Belakang --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Belakang</label>
                            <input type="text" name="last_name"
                                class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name', $guest->last_name ?? '') }}"
                                placeholder="Contoh: Downey Jr.">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor HP --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="mdi mdi-phone"></i></span>
                                <input type="text" name="phone_number"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    value="{{ old('phone_number', $guest->phone_number ?? '') }}"
                                    placeholder="0812xxxx" required>
                            </div>
                            @error('phone_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="mdi mdi-email"></i></span>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $guest->email ?? '') }}"
                                    placeholder="nama@email.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="{{ route('hotel::guest.index') }}" class="btn btn-light px-4">Kembali</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="mdi mdi-content-save me-1"></i> Simpan Data Tamu
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

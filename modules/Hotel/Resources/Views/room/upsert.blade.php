@extends('hotel::layouts.default')

@section('title', ($room ? 'Edit' : 'Tambah') . ' Kamar | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="mdi {{ $room ? 'mdi-pencil' : 'mdi-plus' }} me-2 text-primary"></i>
                    {{ $room ? 'Edit Kamar: ' . $room->room_number : 'Tambah Kamar Baru' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ $room ? route('hotel::room.update', $room->id) : route('hotel::room.store') }}" method="POST">
                    @csrf
                    @if($room)
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor Kamar</label>
                            <input type="text" name="room_number"
                                class="form-control @error('room_number') is-invalid @enderror"
                                value="{{ old('room_number', $room->room_number ?? '') }}"
                                placeholder="Contoh: 101" required>
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Lantai --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lantai</label>
                            <input type="number" name="floor"
                                class="form-control @error('floor') is-invalid @enderror"
                                value="{{ old('floor', $room->floor ?? '1') }}"
                                min="1" required>
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipe Kamar --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Tipe Kamar</label>
                            <select name="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('room_type_id', $room->room_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} - Rp {{ number_format($type->base_price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Kamar --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Status Awal</label>
                            <div class="row g-2">
                                @foreach($statuses as $status)
                                    <div class="col-md-6">
                                        <div class="form-check rounded p-2 px-3">
                                            <input class="form-check-input" type="radio" name="status"
                                                id="status{{ $status->value }}" value="{{ $status->value }}"
                                                {{ old('status', $room->status->value ?? 1) == $status->value ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="status{{ $status->value }}">
                                                {{ $status->label() }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="{{ route('hotel::room.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="mdi mdi-content-save me-1"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-check:hover { background-color: #f8f9fa; cursor: pointer; }
    .form-check-input:checked + .form-check-label { font-weight: bold; color: var(--bs-primary); }
</style>
@endpush

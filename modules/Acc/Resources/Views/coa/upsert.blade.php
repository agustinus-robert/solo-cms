@extends('acc::layouts.default')

@section('title', ($coa ? 'Edit' : 'Tambah') . ' Akun | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="mdi {{ $coa ? 'mdi-pencil' : 'mdi-plus' }} me-2"></i>
                    {{ $coa ? 'Edit Akun' : 'Tambah Akun Baru' }}
                </h5>
            </div>
            <form action="{{ $coa ? route('acc::coa.update', $coa->id) : route('acc::coa.store') }}" method="POST">
                @csrf
                @if($coa) @method('PUT') @endif

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Kode Akun <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code', $coa->code ?? '') }}" placeholder="Contoh: 1101" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="" disabled {{ !$coa ? 'selected' : '' }}>-- Pilih Kategori --</option>
                                @foreach(\Modules\Acc\Enums\AccountCategory::cases() as $cat)
                                    <option value="{{ $cat->value }}"
                                        {{ (old('category', $coa->category->value ?? '') == $cat->value) ? 'selected' : '' }}>
                                        {{ strtoupper($cat->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Saldo Normal <span class="text-danger">*</span></label>
                            <select name="normal_balance" id="normal_balance" class="form-select @error('normal_balance') is-invalid @enderror" required>
                                <option value="" disabled {{ !$coa ? 'selected' : '' }}>-- Pilih Saldo --</option>
                                @foreach(\Modules\Acc\Enums\NormalBalance::cases() as $nb)
                                    <option value="{{ $nb->value }}"
                                        {{ (old('normal_balance', $coa->normal_balance->value ?? '') == $nb->value) ? 'selected' : '' }}>
                                        {{ $nb->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('normal_balance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $coa->name ?? '') }}" placeholder="Contoh: Kas Utama" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('acc::coa.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('category').addEventListener('change', function() {
        const category = this.value;
        const balanceSelect = document.getElementById('normal_balance');

        if (['asset', 'expense'].includes(category)) {
            balanceSelect.value = 'debit';
        } else {
            balanceSelect.value = 'credit';
        }
    });
</script>
@endpush

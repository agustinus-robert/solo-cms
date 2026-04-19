@extends('hotel::layouts.default')

@section('title', ($source ? 'Edit' : 'Tambah') . ' Sumber Reservasi | ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('hotel::source.index') }}" class="btn btn-light border me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0">{{ $source ? 'Edit' : 'Tambah' }} Sumber Reservasi</h4>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $source ? route('hotel::source.update', $source->id) : route('hotel::source.store') }}" method="POST">
                    @csrf
                    @if($source) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Sumber</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $source->name ?? '') }}"
                            placeholder="Contoh: Traveloka, Tiket.com, Direct Booking" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Rate Komisi (%)</label>
                        <div class="input-group">
                            <input type="number" name="commission_rate" id="commission_rate"
                                class="form-control @error('commission_rate') is-invalid @enderror"
                                value="{{ old('commission_rate', $source->commission_rate ?? 0) }}"
                                step="0.01" min="0" max="100" placeholder="0" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                        <div class="form-text small mt-2 p-2 bg-light rounded border border-dashed">
                            <i class="mdi mdi-information-outline text-info me-1"></i>
                            Simulasi: Jika harga kamar <strong>Rp 1.000.000</strong>, maka potongan komisi adalah
                            <strong class="text-danger" id="commission-preview">Rp 0</strong>.
                        </div>
                        @error('commission_rate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hotel::source.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    /**
     * Simulasi perhitungan komisi biar user dapet gambaran
     */
    const rateInput = document.getElementById('commission_rate');
    const previewText = document.getElementById('commission-preview');

    function calculatePreview() {
        const rate = parseFloat(rateInput.value) || 0;
        const simulationAmount = 1000000;
        const result = (rate / 100) * simulationAmount;

        previewText.innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(result);
    }

    rateInput.addEventListener('input', calculatePreview);
    calculatePreview();
</script>
@endpush

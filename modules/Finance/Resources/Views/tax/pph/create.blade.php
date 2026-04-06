@extends('finance::layouts.default')

@section('title', 'Tambah PPh 21 | ')
@section('navtitle', 'Tambah PPh 21')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-12">
            <div class="d-flex align-items-center mb-4">
                <a class="text-decoration-none" href="{{ request('next', route('finance::tax.income-taxs.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
                <div class="ms-4">
                    <h2 class="mb-1">Tambah PPh 21</h2>
                    <div class="text-secondary">Silakan isi formulir di bawah ini untuk menambah PPh 21</div>
                </div>
            </div>
            <div class="card mb-4 border-0">
                <div class="card-body">
                    <i class="mdi mdi-calendar-multiselect"></i> Form PPh 21
                </div>
                <div class="card-body mb-4">
                    <form class="form-block" action="{{ route('finance::tax.income-taxs.store', ['empl_id' => $employee->id, 'next' => request('next', route('finance::tax.income-taxs.index'))]) }}" method="POST" enctype="multipart/form-data"> @csrf
                        <div class="row required mb-3">
                            <label class="col-lg-2 col-xl-2 col-form-label">Nama karyawan</label>
                            <div class="col-xl-8 col-xxl-8">
                                <input class="form-control text-muted" value="{{ $employee->user->name }}">
                            </div>
                        </div>
                        <div class="row required mb-3">
                            <label class="col-lg-2 col-xl-2 col-form-label">Tipe</label>
                            <div class="col-xl-8 col-xxl-8">
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->value }}" @selected($type == Modules\HRMS\Enums\TaxTypeEnum::YEARLY)>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <small class="text-danger d-block"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                        <div class="row required mb-3">
                            <label class="col-lg-2 col-xl-2 col-form-label">Periode</label>
                            <div class="col-xl-12 col-xxl-8">
                                <div class="input-group form-calculate mb-2">
                                    <input type="datetime-local" class="form-control" name="start_at" value="{{ old('start_at', $start_at) }}">
                                    <div class="input-group-text">s.d.</div>
                                    <input type="datetime-local" class="form-control" name="end_at" value="{{ old('end_at', $end_at) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-3 col-xl-2 col-form-label required">Pendapatan</label>
                            <div class="col-lg-9 col-xl-10 col-xxl-10">
                                <div class="card mb-0">
                                    @include('finance::tax.pph.components.earnings', ['earnings' => $earnings])
                                </div>
                            </div>
                            @error('components')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        @if (count($reductions) > 0)
                            <div class="row mb-3">
                                <label class="col-lg-3 col-xl-2 col-form-label required">Potongan</label>
                                <div class="col-lg-9 col-xl-10 col-xxl-10">
                                    <div class="card mb-0">
                                        @include('finance::tax.pph.components.reductions', ['reductions' => $reductions])
                                    </div>
                                </div>
                                @error('components')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif

                        <div class="row mb-3">
                            <label class="col-lg-3 col-xl-2 col-form-label required">Penghitungan PPh </label>
                            <div class="col-lg-9 col-xl-10 col-xxl-10">
                                <div class="card mb-0">
                                    @include('finance::tax.pph.components.pph')
                                </div>
                            </div>
                            @error('components')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-3 col-xl-2 col-form-label">Hasil Pajak (PPh 21)</label>
                            <div class="col-lg-9 col-xl-10 col-xxl-10">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="card card-body border-info mb-0">
                                            <small class="text-info fw-bold text-uppercase">Potongan Per Bulan</small>
                                            <h4 class="mb-0">Rp <span class="pph-monthly-text text-info">0</span></h4>

                                            <input type="number" name="pphtotal" class="d-none calc-pph-monthly-hidden" value="0">

                                            <div class="small text-muted"><cite>Nilai ini yang akan masuk ke rekap gaji</cite></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="card card-body border-secondary mb-0">
                                            <small class="text-secondary fw-bold text-uppercase">Total Per Tahun</small>
                                            <h4 class="mb-0">Rp <span class="pph-input text-secondary">0</span></h4>
                                            <div class="small text-muted"><cite>Terbilang: <span class="pph-inwords"></span> rupiah</cite></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="offset-lg-3 offset-xl-2 col-lg-9 col-xl-10">
                                <div class="card card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" id="recap" type="checkbox" name="as_recap" value="1">
                                        <label class="form-check-label" for="recap">Masukan kedalam rekap penggajian sesuai periode terpilih.</label>
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" id="agreement" type="checkbox" name="validated" value="1" required>
                                    <label class="form-check-label" for="agreement">Dengan ini saya selaku Keuangan (Finance) menyatakan data di atas adalah valid.</label>
                                </div>
                                <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                                <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('finance::tax.income-taxs.index')) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
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
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush

@push('scripts')
<script>
    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID').format(Math.floor(angka));

    const terbilang = (angka) => {
        const bilne = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if (angka < 12) return bilne[angka];
        else if (angka < 20) return terbilang(angka - 10) + " belas";
        else if (angka < 100) return terbilang(Math.floor(angka / 10)) + " puluh " + terbilang(angka % 10);
        else if (angka < 200) return "seratus " + terbilang(angka - 100);
        else if (angka < 1000) return terbilang(Math.floor(angka / 100)) + " ratus " + terbilang(angka % 100);
        else if (angka < 2000) return "seribu " + terbilang(angka - 1000);
        else if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + " ribu " + terbilang(angka % 1000);
        else if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + " juta " + terbilang(angka % 1000000);
        return "";
    }

   const calculateAll = () => {
    let grandTotalIncomeYear = 0;

    document.querySelectorAll('.calc-income-tbody').forEach(tbody => {
        let subtotalIncome = 0;
        tbody.querySelectorAll('.calc-row').forEach(row => {
            const input = row.querySelector('.items-yearly');
            const checkbox = row.querySelector('.form-check-input');
            if(input && (checkbox ? checkbox.checked : true)) {
                subtotalIncome += parseFloat(input.value) || 0;
            }
        });

        const subInput = tbody.querySelector('.calc-income-year-subtotal-input');
        if(subInput) subInput.value = subtotalIncome;

        const subDisplay = tbody.querySelector('.calc-income-year-subtotal-display');
        if(subDisplay) subDisplay.value = formatRupiah(subtotalIncome);

        grandTotalIncomeYear += subtotalIncome;
    });

    let grandTotalReductionYear = 0;
    document.querySelectorAll('.calc-reduction-tbody').forEach(tbody => {
        tbody.querySelectorAll('.calc-row').forEach(row => {
            const input = row.querySelector('.reduction-year-amount');
            const checkbox = row.querySelector('.form-check-input');
            if(input && (checkbox ? checkbox.checked : true)) {
                grandTotalReductionYear += parseFloat(input.value) || 0;
            }
        });
    });

    const ptkp = {{ (int) $ptkp }};
    const pkpYearly = Math.max(0, grandTotalIncomeYear - grandTotalReductionYear - ptkp);

    const brutoInput = document.querySelector('.calc-bruto-month-subtotal-input');
    if(brutoInput) {
        brutoInput.value = grandTotalIncomeYear;
        const brutoMonthlyDisplay = document.querySelector('.calc-bruto-monthly-display');
        if(brutoMonthlyDisplay) brutoMonthlyDisplay.value = formatRupiah(grandTotalIncomeYear / 12);
    }

    const pengurangDisplay = document.querySelector('.calc-total-pengurang-display');
    if(pengurangDisplay) pengurangDisplay.value = formatRupiah(ptkp + grandTotalReductionYear);

    const pkpInput = document.querySelector('.calc-pkp-value-input');
    if(pkpInput) {
        pkpInput.value = Math.floor(pkpYearly);
        const pkpMonthlyDisplay = document.querySelector('.calc-pkp-monthly-display');
        if(pkpMonthlyDisplay) pkpMonthlyDisplay.value = formatRupiah(pkpYearly / 12);
    }

    calculateTaxLayers(pkpYearly);
}

const calculateTaxLayers = (pkp) => {
    const isNpwp = {{ $is_npwp ? 'true' : 'false' }};
    const categories = @json($categories->map(fn($c) => [
        'min' => $c->getMin(),
        'max' => $c->getMax(),
        'rate' => $is_npwp ? $c->getPercentage() : $c->getPercentageNonNpwp()
    ]));

    let remainingPkp = pkp;
    let totalPphYear = 0;

    categories.forEach((c, index) => {
        const idx = index + 1;
        const range = c.max - c.min;
        const taxable = Math.min(remainingPkp, range);
        let layerPph = 0;

        if (taxable > 0) {
            layerPph = Math.floor(taxable * (c.rate / 100));
            totalPphYear += layerPph;
            remainingPkp -= taxable;
        }

        const inputLayer = document.querySelector(`.calc-pph${idx}-value-input`);
        if(inputLayer) inputLayer.value = layerPph;

        const displayLayer = document.querySelector(`.calc-pph${idx}-monthly-display`);
        if(displayLayer) displayLayer.value = formatRupiah(layerPph / 12);
    });

    const pphMonthly = Math.floor(totalPphYear / 12);
    const monthlyText = document.querySelector('.pph-monthly-text');
    if(monthlyText) monthlyText.innerText = formatRupiah(pphMonthly);

    const hiddenTotal = document.querySelector('.calc-pph-monthly-hidden');
    if(hiddenTotal) hiddenTotal.value = pphMonthly;

    const yearlyText = document.querySelector('.pph-input');
    if(yearlyText) yearlyText.innerText = formatRupiah(totalPphYear);

    const terMonthly = document.querySelector('.calc-ter-amount-monthly-display');
    if(terMonthly) terMonthly.value = formatRupiah(pphMonthly);

    const terYearly = document.querySelector('.calc-ter-amount-input');
    if(terYearly) terYearly.value = totalPphYear;

    const inwords = document.querySelector('.pph-inwords');
    if(inwords) inwords.innerText = terbilang(pphMonthly);
}
</script>
@endpush

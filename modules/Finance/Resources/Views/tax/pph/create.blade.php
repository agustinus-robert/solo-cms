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

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush

@push('scripts')
<script>
    // 1. Fungsi format rupiah untuk tampilan teks (dengan titik)
    const formatRupiah = (angka) => {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0
        }).format(angka);
    }

    // 2. Fungsi Terbilang Indonesia
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

    // 3. Fungsi Utama: Hitung Subtotal & Baris Tahunan
    const sumSubTotal = (event) => {
        const el = event.target || event;
        const tbody = el.closest('.calc-income-tbody');
        if (!tbody) return;

        // Update angka tahunan (x12) di baris yang sedang diedit
        const row = el.closest('.calc-row');
        if (row) {
            const monthlyVal = parseFloat(el.value) || 0;
            const yearlyDisplay = row.querySelector('.item-yearly-display');
            if (yearlyDisplay) {
                yearlyDisplay.value = formatRupiah(monthlyVal * 12);
            }
        }

        const items = tbody.querySelectorAll('.items-monthly');
        let subtotalValue = 0;

        items.forEach(input => {
            subtotalValue += parseFloat(input.value) || 0;
        });

        // Update Input Subtotal Bulanan (Angka murni buat DB)
        const subtotalInput = tbody.querySelector('.calc-income-month-subtotal-input');
        if (subtotalInput) subtotalInput.value = subtotalValue;

        // Update Display Subtotal Tahunan (x12)
        const subtotalYearDisplay = tbody.querySelector('.calc-income-year-subtotal-display');
        if (subtotalYearDisplay) {
            subtotalYearDisplay.value = formatRupiah(subtotalValue * 12);
        }

        // Update Teks Terbilang & Label Rupiah Bulanan
        const subtotalInword = tbody.querySelector('.items-monthly-inword');
        if (subtotalInword) {
            const labelRupiah = formatRupiah(subtotalValue);
            subtotalInword.innerHTML = `<strong>Rp ${labelRupiah}</strong> (${subtotalValue > 0 ? terbilang(subtotalValue) : "nol"} rupiah)`;
        }

        updateGrandTotalIncome();
    }

    // 4. Update Total Keseluruhan Pendapatan
    const updateGrandTotalIncome = () => {
        const allCategorySubtotals = document.querySelectorAll('.calc-income-month-subtotal-input');
        let grandTotal = 0;

        allCategorySubtotals.forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });

        // Update Field Total Akhir Bulanan
        const totalIncomeInput = document.querySelector('.calc-totalincome-month-subtotal-input');
        if (totalIncomeInput) totalIncomeInput.value = grandTotal;

        // Update Display Total Akhir Tahunan
        const totalYearDisplay = document.querySelector('.calc-totalincome-year-subtotal-display');
        if (totalYearDisplay) {
            totalYearDisplay.value = formatRupiah(grandTotal * 12);
        }

        // Update Terbilang Total Akhir
        const totalInword = document.querySelector('.totalincome-month-inword');
        if (totalInword) {
            const labelRupiah = formatRupiah(grandTotal);
            totalInword.innerHTML = `<strong>Rp ${labelRupiah}</strong> (${grandTotal > 0 ? terbilang(grandTotal) : "nol"} rupiah)`;
        }

        // Sinkron ke PPh (Input bruto biasanya tahunan)
        const brutoYearlyInput = document.querySelector('.calc-bruto-month-subtotal-input');
        if (brutoYearlyInput) {
            brutoYearlyInput.value = grandTotal * 12;
            calculatePph();
        }
    }

    // 5. Kalkulasi PPh 21
    const calculatePph = () => {
        const ptkpAmount = @json($ptkp); 
        const isNpwp = @json($is_npwp);
        const brutoInput = document.querySelector('.calc-bruto-month-subtotal-input');
        if (!brutoInput) return;

        const brutoTahun = parseFloat(brutoInput.value) || 0;
        const pkpTahun = Math.max(0, brutoTahun - ptkpAmount);

        // Update PKP Displays
        if(document.querySelector('.calc-pkp-value-input')) 
            document.querySelector('.calc-pkp-value-input').value = Math.floor(pkpTahun);
        
        if(document.querySelector('.calc-bruto-monthly-display'))
            document.querySelector('.calc-bruto-monthly-display').value = formatRupiah(Math.floor(brutoTahun / 12));
        
        if(document.querySelector('.calc-pkp-monthly-display'))
            document.querySelector('.calc-pkp-monthly-display').value = formatRupiah(Math.floor(pkpTahun / 12));

        const categories = [
            @foreach ($categories as $cat)
            {
                idx: {{ $loop->iteration }},
                min: {{ $cat->getMin() }},
                max: {{ $cat->getMax() }},
                rate: {{ $is_npwp ? $cat->getPercentage() : $cat->getPercentageNonNpwp() }} / 100
            },
            @endforeach
        ];

        let remainingPkp = pkpTahun;
        let totalPphTahun = 0;

        categories.forEach(c => {
            const range = c.max - c.min;
            const taxable = Math.min(remainingPkp, range);
            let layerPphTahun = 0;

            if (taxable > 0) {
                layerPphTahun = Math.floor(taxable * c.rate);
                totalPphTahun += layerPphTahun;
                remainingPkp -= taxable;
            }

            const layerTahunEl = document.querySelector(`.calc-pph${c.idx}-value-input`);
            if (layerTahunEl) layerTahunEl.value = layerPphTahun;

            const layerBulanEl = document.querySelector(`.calc-pph${c.idx}-monthly-display`);
            if (layerBulanEl) layerBulanEl.value = formatRupiah(Math.floor(layerPphTahun / 12));
        });

        const totalPphBulanan = Math.floor(totalPphTahun / 12);

        // Update UI Akhir
        const pphTotalHidden = document.querySelector('.calc-pph-monthly-hidden');
        if (pphTotalHidden) pphTotalHidden.value = totalPphBulanan;

        const pphMonthText = document.querySelector('.pph-monthly-text');
        if (pphMonthText) pphMonthText.innerText = formatRupiah(totalPphBulanan);

        const pphTahunText = document.querySelector('.pph-input');
        if (pphTahunText) pphTahunText.innerText = formatRupiah(totalPphTahun);

        const pphInwords = document.querySelector('.pph-inwords');
        if (pphInwords) {
            pphInwords.innerText = totalPphBulanan > 0 ? terbilang(totalPphBulanan) : "nol";
        }
    }

    // Init awal pas halaman kelar loading
    document.addEventListener('DOMContentLoaded', () => {
        const allInputs = document.querySelectorAll('.items-monthly');
        allInputs.forEach(input => {
            // Trigger hitungan buat setiap baris awal
            sumSubTotal(input);
        });
    });
</script>
@endpush
@extends('finance::layouts.default')

@section('title', 'Tambah PPh 21 | ')
@section('navtitle', 'Tambah PPh 21')

@section('content')
<div class="container-fluid pb-5">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-12">
            <div class="d-flex align-items-center mb-4">
                <a class="btn btn-outline-primary border-0 p-2 shadow-sm rounded-circle" href="{{ request('next', route('finance::tax.ter-taxs.index')) }}">
                    <i class="mdi mdi-arrow-left mdi-24px"></i>
                </a>
                <div class="ms-3">
                    <h3 class="mb-0 fw-bold text-dark">Tambah PPh 21</h3>
                    <p class="text-muted mb-0">Silakan lengkapi detail pajak penghasilan di bawah ini.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-danger p-2 rounded-3 me-3">
                            <i class="mdi mdi-calendar-multiselect text-danger mdi-24px"></i>
                        </div>
                        <h5 class="card-title mb-0">Formulir PPh 21 (TER)</h5>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form class="form-block form-confirm" action="{{ route('finance::tax.ter-taxs.store', ['empl_id' => $employee->id, 'next' => request('next', route('finance::tax.ter-taxs.index'))]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="bg-light rounded-3 p-4 mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small text-uppercase fw-semibold text-muted d-block mb-1">Nama Karyawan</label>
                                    <input class="form-control border-0 bg-white fw-bold text-dark" value="{{ $employee->user->name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-uppercase fw-semibold text-muted d-block mb-1 required">Tipe Pajak</label>
                                    <select class="form-select border-0 shadow-sm @error('type') is-invalid @enderror" name="type" required>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->value }}" @selected($type == Modules\HRMS\Enums\TaxTypeEnum::YEARLY)>{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <small class="text-danger d-block mt-1"> {{ $message }} </small>
                                    @enderror
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="small text-uppercase fw-semibold text-muted d-block mb-1 required">Periode Laporan</label>
                                    <div class="input-group form-calculate shadow-sm rounded-2 overflow-hidden">
                                        <input type="datetime-local" class="form-control border-0" name="start_at" value="{{ old('start_at', $start_at) }}">
                                        <span class="input-group-text bg-white border-0 text-muted px-3">s.d.</span>
                                        <input type="datetime-local" class="form-control border-0" name="end_at" value="{{ old('end_at', $end_at) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-lg-3 col-form-label fw-bold required text-primary">
                                <i class="mdi mdi-cash-plus me-1"></i>Komponen Pendapatan
                            </label>
                            <div class="col-lg-9">
                                <div class="card border shadow-none mb-0 overflow-hidden">
                                    @include('finance::tax.ters.components.earnings', ['earnings' => $earnings])
                                </div>
                                @error('components')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        @if (count($reductions) > 0)
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label fw-bold required text-danger">
                                    <i class="mdi mdi-cash-minus me-1"></i>Komponen Potongan
                                </label>
                                <div class="col-lg-9">
                                    <div class="card border shadow-none mb-0 overflow-hidden">
                                        @include('finance::tax.ters.components.reductions', ['reductions' => $reductions])
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <label class="col-lg-3 col-form-label fw-bold required text-dark">
                                <i class="mdi mdi-calculator me-1"></i>Rangkuman PPh 21
                            </label>
                            <div class="col-lg-9">
                                <div class="card border shadow-none mb-0 overflow-hidden">
                                    @include('finance::tax.ters.components.pph')
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-lg-3 col-form-label fw-bold fs-5 text-danger">Total Pajak (PPh 21)</label>
                            <div class="col-lg-9">
                                <div class="card bg-soft-danger border-danger border-dashed p-4 shadow-none text-center">
                                    <input class="d-none" type="number" name="pphtotal" value="0">
                                    <h2 class="display-6 fw-bold text-danger mb-1">Rp <span class="pph-input">0</span></h2>
                                    <p class="text-muted mb-0 small">
                                        <i class="mdi mdi-format-quote-open me-1"></i>
                                        Terbilang: <span class="pph-inword text-capitalize">Nol</span> Rupiah
                                        <i class="mdi mdi-format-quote-close ms-1"></i>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="offset-lg-3 col-lg-9">
                                <div class="card border bg-light shadow-none mb-4">
                                    <div class="card-body py-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input ms-0 me-3" id="recap" type="checkbox" name="as_recap" value="1">
                                            <label class="form-check-label fw-semibold" for="recap">Masukan kedalam rekap penggajian sesuai periode terpilih.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input ms-0 me-3" id="agreement" type="checkbox" name="validated" value="1" required>
                                            <label class="form-check-label fw-semibold" for="agreement">Dengan ini saya selaku Keuangan (Finance) menyatakan data di atas adalah valid.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-danger btn-lg px-5 shadow-sm">
                                        <i class="mdi mdi-check-circle-outline me-2"></i>Simpan Laporan
                                    </button>
                                    <a class="btn btn-light btn-lg" href="{{ request('next', route('finance::tax.ter-taxs.index')) }}">
                                        <i class="mdi mdi-arrow-left me-1"></i>Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .bg-soft-danger { background-color: rgba(var(--bs-danger-rgb), 0.1); }
        .bg-soft-primary { background-color: rgba(var(--bs-primary-rgb), 0.1); }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .form-select, .form-control { transition: all 0.2s ease; }
        .form-select:focus, .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(var(--bs-danger-rgb), 0.15); border-color: var(--bs-danger); }
    </style>
@endpush

@push('scripts')
    <script>
        // --- TETAP GUNAKAN LOGIKA ASLI ANDA ---
        const setVal = (selector, val) => { let el = document.querySelector(selector); if(el) el.value = val; };
        const setHtml = (selector, val) => { let el = document.querySelector(selector); if(el) el.innerHTML = val; };

        const sumSubTotal = () => {
            [...document.querySelectorAll('.calc-income-tbody')].forEach(tbody => {
                let items = [...tbody.querySelectorAll('.items-monthly')];
                let submonth = Math.abs(items.map(el => parseFloat(el.value || 0)).reduce((result, x) => result + x, 0));
                let subInput = tbody.querySelector('.calc-income-month-subtotal-input');
                if(subInput) subInput.value = submonth;
                let subInword = tbody.querySelector('.items-monthly-inword');
                if(subInword) subInword.innerHTML = typeof terbilang === 'function' ? terbilang(submonth).toLowerCase() : submonth;
            });

            let incomeInputs = [...document.querySelectorAll('.calc-income-month-subtotal-input')];
            let thpmonth = Math.round(incomeInputs.map(el => parseFloat(el.value || 0)).reduce((thp, x) => thp + x, 0));
            setVal('.calc-totalincome-month-subtotal-input', thpmonth);
            setHtml('.totalincome-month-inword', typeof terbilang === 'function' ? terbilang(Math.abs(thpmonth)).toLowerCase() : thpmonth);

            calculateReduction();
        }

        const calculateReduction = () => {
            let reducemonth = 0;
            let reductionInputs = [...document.querySelectorAll('.reduction-month-amount')];
            if (reductionInputs.length > 0) {
                reducemonth = Math.round(reductionInputs.map(el => parseFloat(el.value || 0)).reduce((thp, x) => thp + x, 0));
                setVal('.calc-reduction-month-subtotal-input', reducemonth);
                setVal('.calc-totalreduction-month-subtotal-input', reducemonth);
                setHtml('.reduction-month-inword', typeof terbilang === 'function' ? terbilang(Math.abs(reducemonth)).toLowerCase() : reducemonth);
            }
            calculateBruto();
        }

        const calculateBruto = () => {
            let totalIncome = document.querySelector('.calc-totalincome-month-subtotal-input')?.value || 0;
            let totalReduction = document.querySelector('.calc-reduction-month-subtotal-input')?.value || 0;
            let brutoInput = document.querySelector('.calc-bruto-month-subtotal-input');
            let bruto = (parseFloat(totalIncome) + parseFloat(totalReduction));

            if (document.activeElement === brutoInput) {
                bruto = parseFloat(brutoInput.value || 0);
            } else {
                if(brutoInput) brutoInput.value = bruto;
            }
            setHtml('.bruto-month-inword', typeof terbilang === 'function' ? terbilang(Math.abs(bruto)).toLowerCase() : bruto);
            calculatePph(bruto);
        }

        const calculatePph = (bruto) => {
            const ters = @JSON($ters);
            const objectives = @JSON($configs);
            if(!ters || !ters.rates) return;

            let brutoVal = parseFloat(bruto);
            let result = ters.rates.find(entry => {
                let lower = parseFloat(entry.lower);
                let upper = entry.upper === null ? Infinity : parseFloat(entry.upper);
                return brutoVal >= lower && brutoVal < upper;
            });

            if (!result) return;

            setVal('.calc-ptkp-category-input', ters.status);
            setVal('.calc-ter-category-input', ters.category);
            setVal('.calc-ter-value-input', result.percentage);

            let pphTotal100 = Math.floor((parseFloat(result.percentage) / 100) * brutoVal);
            let pphKaryawan = 0;

            Object.values(objectives).forEach((ar) => {
                let objInput = document.querySelector(`.calc-${ar.key}-value-input`);
                if(objInput) {
                    let sharePercent = parseFloat(ar.rate || 0);
                    let shareAmount = Math.floor((sharePercent / 100) * pphTotal100);
                    objInput.value = shareAmount;
                    setHtml(`.calc-${ar.key}-value-inword`, typeof terbilang === 'function' ? terbilang(shareAmount).toLowerCase() : shareAmount);
                    if(ar.key === 'employee') pphKaryawan = shareAmount;
                }
            });

            setVal('[name="pphtotal"]', pphKaryawan);
            setHtml('.pph-input', pphKaryawan.toLocaleString('id-ID'));
            setHtml('.pph-inword', typeof terbilang === 'function' ? terbilang(pphKaryawan).toLowerCase() : pphKaryawan);
        }

        document.addEventListener('DOMContentLoaded', () => { sumSubTotal(); });
        document.addEventListener('input', (e) => {
            if (e.target.closest('.form-calculate') ||
                e.target.classList.contains('items-monthly') ||
                e.target.classList.contains('reduction-month-amount') ||
                e.target.classList.contains('calc-bruto-month-subtotal-input')) {
                sumSubTotal();
            }
        });
    </script>
@endpush

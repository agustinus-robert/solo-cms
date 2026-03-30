@extends('finance::layouts.default')

@section('title', 'Tambah PPh 21 | ')
@section('navtitle', 'Tambah PPh 21')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-12">
            <div class="d-flex align-items-center mb-4">
                <a class="text-decoration-none" href="{{ request('next', route('finance::tax.ter-taxs.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
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
                    <form class="form-block form-confirm" action="{{ route('finance::tax.ter-taxs.store', ['empl_id' => $employee->id, 'next' => request('next', route('finance::tax.ter-taxs.index'))]) }}" method="POST" enctype="multipart/form-data"> @csrf
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
                                    @include('finance::tax.ters.components.earnings', ['earnings' => $earnings])
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
                                        @include('finance::tax.ters.components.reductions', ['reductions' => $reductions])
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
                                    @include('finance::tax.ters.components.pph')
                                </div>
                            </div>
                            @error('components')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-3 col-xl-2 col-form-label">Pajak penghasilan (PPh21)</label>
                            <div class="col-lg-8 col-xl-7 col-xxl-6">
                                <div class="card card-body mb-0">
                                    <input class="d-none" type="number" name="pphtotal" value="0">
                                    <h4>Rp <span class="pph-input"></span></h4>
                                    <div class="small text-muted"><cite>Terbilang: <span class="pph-inword"></span> rupiah</cite></div>
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
                                <div>
                                    <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                                    <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('finance::tax.ter-taxs.index')) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                                </div>
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
        /* Chrome, Safari, Edge, Opera */
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
        // Fungsi pembantu untuk update UI agar tidak crash
        const setVal = (selector, val) => { let el = document.querySelector(selector); if(el) el.value = val; };
        const setHtml = (selector, val) => { let el = document.querySelector(selector); if(el) el.innerHTML = val; };

        const sumSubTotal = () => {
            // 1. Hitung Subtotal per Tbody (Pendapatan)
            [...document.querySelectorAll('.calc-income-tbody')].forEach(tbody => {
                let items = [...tbody.querySelectorAll('.items-monthly')];
                let submonth = Math.abs(items.map(el => parseFloat(el.value || 0)).reduce((result, x) => result + x, 0));
                
                let subInput = tbody.querySelector('.calc-income-month-subtotal-input');
                if(subInput) subInput.value = submonth;
                
                let subInword = tbody.querySelector('.items-monthly-inword');
                if(subInword) subInword.innerHTML = typeof terbilang === 'function' ? terbilang(submonth).toLowerCase() : submonth;
            });

            // 2. Hitung Total Income
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
            
            // Ambil bruto (bisa dari hitungan otomatis atau input manual)
            let brutoInput = document.querySelector('.calc-bruto-month-subtotal-input');
            let bruto = (parseFloat(totalIncome) + parseFloat(totalReduction));
            
            // Jika user input manual di field bruto, gunakan nilai itu
            if (document.activeElement === brutoInput) {
                bruto = parseFloat(brutoInput.value || 0);
            } else {
                if(brutoInput) brutoInput.value = bruto;
            }

            setHtml('.bruto-month-inword', typeof terbilang === 'function' ? terbilang(Math.abs(bruto)).toLowerCase() : bruto);
            calculatePph(bruto);
        }

        const calculatePph = (bruto) => {
            const ters = @JSON($ters); // Data dari Controller
            const objectives = @JSON($configs); // Config 50/50 mu

            if(!ters || !ters.rate) return;

            let brutoVal = parseFloat(bruto);

            // Cari rate berdasarkan range bruto
            let result = ters.rate.find(entry => 
                brutoVal >= parseFloat(entry.lower) && 
                (entry.upper === null || brutoVal < parseFloat(entry.upper))
            );

            if (!result) return;

            // --- BAGIAN BIAR STATUS & KATEGORI NGGAK HILANG ---
            setVal('.calc-ptkp-category-input', ters.status); // Munculin TK/0
            setVal('.calc-ter-category-input', ters.ter);    // Munculin A
            setVal('.calc-ter-value-input', result.percentage); // Munculin Tarif 1.75 atau 2.25
            
            // Hitung PPh TOTAL (100%)
            let pphTotal100 = Math.floor((parseFloat(result.percentage) * brutoVal) / 100);
            
            let pphKaryawan = 0;

            // Distribusi 50/50 sesuai config
            Object.values(objectives).forEach((ar) => {
                let objInput = document.querySelector(`.calc-${ar.key}-value-input`);
                if(objInput) {
                    let sharePercent = parseFloat(ar.rate || 0);
                    let shareAmount = Math.floor((sharePercent / 100) * pphTotal100);
                    
                    objInput.value = shareAmount;
                    setHtml(`.calc-${ar.key}-value-inword`, typeof terbilang === 'function' ? terbilang(shareAmount).toLowerCase() : shareAmount);

                    // Simpan porsi karyawan untuk box paling bawah
                    if(ar.key === 'employee') {
                        pphKaryawan = shareAmount;
                    }
                }
            });

            // Tampilkan PPh Total yang SUDAH DIBAGI 2 (Porsi Karyawan) di box bawah
            setVal('[name="pphtotal"]', pphKaryawan); 
            setHtml('.pph-input', pphKaryawan.toLocaleString('id-ID'));
            setHtml('.pph-inword', typeof terbilang === 'function' ? terbilang(pphKaryawan).toLowerCase() : pphKaryawan);
        }

        // Jalankan saat pertama load
        document.addEventListener('DOMContentLoaded', () => {
            sumSubTotal();
        });

        // Jalankan otomatis setiap kali user mengetik angka
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
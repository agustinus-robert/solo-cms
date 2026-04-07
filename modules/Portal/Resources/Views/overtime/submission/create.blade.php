@extends('portal::layouts.index')

@section('title', 'Buat Pengajuan Lembur | ' . env('APP_NAME'))

@section('navtitle', 'Lembur')

@include('components.tourguide', [
    'steps' => array_values(
        array_filter(
            [
                ['selector' => '.tg-steps-overtime-name', 'title' => 'Pekerjaan', 'content' => 'Tulis apa pekerjaan yang telah kamu lakukan.'],
                ['selector' => '.tg-steps-overtime-dates', 'title' => 'Tanggal dan waktu lembur', 'content' => 'Isi juga tanggal dan waktu lembur kamu.'],
                ['selector' => '.tg-steps-overtime-description', 'title' => 'Deskripsi', 'content' => 'Bisa diisi realisasi lembur, catatan, alasan, atau deskripsi penting lainnya kalau ada.'],
                ['selector' => '.tg-steps-overtime-attachment', 'title' => 'Lampiran berkas', 'content' => 'Kalau ada lampiran bisa diunggah di sini, misalnya screenshot pekerjaan atau lainnya.'],
                ['disabled' => count($superiors) == 0, 'selector' => '.tg-steps-overtime-approvers', 'title' => 'Persetujuan', 'content' => 'Pengajuan lembur yang kamu buat akan dicek sama atasan yang kamu pilih.'],
            ],
            fn($step) => !($step['disabled'] ?? false))),
])

@section('contents')
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                 <div class="navbar-brand-box">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('skote/images/logo.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('skote/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('skote/images/logo-light.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('skote/images/logo-light.png') }}" alt="" height="39">
                        </span>
                    </a>
                </div>
                <button type="button" class="btn btn-sm font-size-16 d-lg-none header-item waves-effect waves-light px-3" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            </div>

            <div class="d-flex">
                @include('layouts.nav-dashboard')
                @include('layouts.shortcut_menu')
                <div class="dropdown d-none d-lg-inline-block ms-1">
                    @include('layouts.nav_name')
                </div>
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row align-items-center mb-4 mt-2">
                    @include('layouts.component.alert-access')

                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::overtime.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Form Pengajuan Lembur</h4>
                                <p class="text-muted mb-0 font-size-13">Silakan lengkapi detail lembur Anda di bawah ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xl-9">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <form class="form-confirm form-block" action="{{ route('portal::overtime.submission.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    {{-- Row 1: Pekerjaan --}}
                                    <div class="row mb-4 tg-steps-overtime-name">
                                        <label class="col-md-3 col-form-label fw-bold">Nama Pekerjaan <span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Contoh: Menghitung stock produk di gudang" required>
                                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- Row 2: Waktu --}}
                                    <div class="row mb-4 tg-steps-overtime-dates">
                                        <div class="col-md-3 d-flex justify-content-between align-items-start">
                                            <label class="col-form-label fw-bold">Waktu Lembur <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <div id="dates-container">
                                                <div class="date-row p-3 bg-light rounded mb-3 border border-dashed position-relative">
                                                    <div class="row g-2">
                                                        <div class="col-sm-5">
                                                            <label class="font-size-11 text-muted text-uppercase fw-bold">Tanggal</label>
                                                            <input type="date" class="form-control form-control-sm" name="dates[d][]" max="{{ date('Y-m-d') }}" value="{{ old('dates.d.0') }}" required>
                                                        </div>
                                                        <div class="col-sm-7">
                                                            <label class="font-size-11 text-muted text-uppercase fw-bold">Jam Kerja</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="time" class="form-control" name="dates[s][]" onchange="changeMinTime(event)" required>
                                                                <span class="input-group-text bg-white">s.d.</span>
                                                                <input type="time" class="form-control" name="dates[e][]" onchange="changeMaxTime(event)" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-soft-primary waves-effect waves-light btn-add-date">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Hari Lembur
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Row 3: Deskripsi & Lampiran --}}
                                    <div class="row mb-4">
                                        <label class="col-md-3 col-form-label fw-bold">Deskripsi Pekerjaan</label>
                                        <div class="col-md-9 tg-steps-overtime-description">
                                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" placeholder="Detail hasil pengerjaan atau alasan lembur...">{{ old('description') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-4 tg-steps-overtime-attachment">
                                        <label class="col-md-3 col-form-label fw-bold">Lampiran Berkas</label>
                                        <div class="col-md-9">
                                            <div class="border border-2 border-dashed rounded p-3 text-center bg-light bg-opacity-50">
                                                <input class="form-control form-control-sm" name="attachment" type="file" accept="image/*,application/pdf">
                                                <p class="text-muted font-size-11 mt-2 mb-0">Format: JPG, PNG, PDF (Maks. 2MB)</p>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    {{-- Row 4: Atasan --}}
                                    <div class="tg-steps-overtime-approvers">
                                        @foreach ($superiors as $superior)
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label fw-bold">{{ $superior['label'] }} <span class="text-danger">*</span></label>
                                                <div class="col-md-9">
                                                    @php
                                                        $firstPos = $superior['positions']->first();
                                                        $isOwner = (count($superior['positions']) == 1 && $firstPos && ($firstPos->level_id ?? $firstPos->level) <= 1);
                                                    @endphp

                                                    @if ($isOwner)
                                                        <div class="p-2 bg-light rounded border border-dashed">
                                                            @foreach ($firstPos->employeePositions as $pEmp)
                                                                <div class="fw-bold text-primary mb-1">
                                                                    <i class="mdi mdi-account-check-outline me-1"></i>
                                                                    {{ $pEmp->employee->user->name }}
                                                                </div>
                                                                <input type="hidden" name="approvables[{{ $superior['step'] }}]" value="{{ $pEmp->id }}">
                                                            @endforeach
                                                            <small class="text-muted italic">(Otomatis Terpilih)</small>
                                                        </div>
                                                    @else
                                                        <select class="form-select @error('approvables.' . $superior['step']) is-invalid @enderror" name="approvables[{{ $superior['step'] }}]" @if ($superior['required']) required @endif>
                                                            @if (count($superior['positions']) > 1 || (count($superior['positions']) == 1 && $firstPos->employeePositions->count() > 1))
                                                                <option value="">-- Pilih Atasan --</option>
                                                            @endif
                                                            @foreach ($superior['positions'] as $position)
                                                                <optgroup label="{{ $position->name }}">
                                                                    @foreach ($position->employeePositions as $pEmp)
                                                                        <option value="{{ $pEmp->id }}" @selected(count($superior['positions']) == 1 && $position->employeePositions->count() == 1)>
                                                                            {{ $pEmp->employee->user->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                    @error('approvables.' . $superior['step']) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-md-9 offset-md-3">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-lg px-5 waves-effect waves-light">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i> Ajukan Sekarang
                                                </button>
                                                <a href="{{ request('next', route('portal::overtime.submission.index')) }}" class="btn btn-light btn-lg px-4">Batal</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <div id="dates-template" class="d-none">
        <div class="date-row p-3 bg-light rounded mb-3 border border-dashed position-relative animate-fade-in">
            <button type="button" class="btn btn-sm btn-soft-danger rounded-circle position-absolute" style="top: -10px; right: -10px;" onclick="this.closest('.date-row').remove()">
                <i class="mdi mdi-close"></i>
            </button>
            <div class="row g-2">
                <div class="col-sm-5">
                    <label class="font-size-11 text-muted text-uppercase fw-bold">Tanggal</label>
                    <input type="date" class="form-control form-control-sm" name="dates[d][]" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-sm-7">
                    <label class="font-size-11 text-muted text-uppercase fw-bold">Jam Kerja</label>
                    <div class="input-group input-group-sm">
                        <input type="time" class="form-control" name="dates[s][]" onchange="changeMinTime(event)" required>
                        <span class="input-group-text bg-white">s.d.</span>
                        <input type="time" class="form-control" name="dates[e][]" onchange="changeMaxTime(event)" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const max_dates = 5;

        let changeMinTime = (e) => {
            let row = e.target.closest('.date-row');
            let endTimeInput = row.querySelector('input[name="dates[e][]"]');
            if(endTimeInput) endTimeInput.min = e.target.value;
        }

        let changeMaxTime = (e) => {
            let row = e.target.closest('.date-row');
            let startTimeInput = row.querySelector('input[name="dates[s][]"]');
            if(startTimeInput) startTimeInput.max = e.target.value;
        }

        window.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.btn-add-date').addEventListener('click', (e) => {
                let container = document.getElementById('dates-container');
                if (container.querySelectorAll('.date-row').length < max_dates) {
                    container.insertAdjacentHTML('beforeend', document.getElementById('dates-template').innerHTML);
                } else {
                    alert('Maksimal pengajuan adalah 5 hari.');
                }
            });
        });
    </script>
@endpush

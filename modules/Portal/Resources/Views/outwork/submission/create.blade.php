@extends('portal::layouts.index')

@section('title', 'Pengajuan Kegiatan | ' . env('APP_NAME'))

@section('navtitle', 'Insentif')

@include('components.tourguide', [
    'steps' => array_values(
        array_filter(
            [
                ['selector' => '.tg-steps-outwork-name', 'title' => 'Nama kegiatan', 'content' => 'Tulis nama aktivitas/kegiatan yang akan diajukan.'],
                ['selector' => '.tg-steps-outwork-category', 'title' => 'Bentuk kegiatan', 'content' => 'Pilih salah satu bentuk kegiatan sesuai dengan aktivitas yang Kamu lakukan.'],
                ['selector' => '.tg-steps-outwork-dates', 'title' => 'Tanggal dan waktu', 'content' => 'Isi juga tanggal dan waktu pelaksanaan kegiatan kamu.'],
                ['selector' => '.tg-steps-outwork-description', 'title' => 'Deskripsi', 'content' => 'Bisa diisi realisasi kegiatan, catatan, alasan, atau deskripsi penting lainnya kalau ada.'],
                ['selector' => '.tg-steps-outwork-attachment', 'title' => 'Lampiran berkas', 'content' => 'Kalau ada lampiran bisa diunggah di sini, misalnya surat tugas/pengantar, screenshot atau lainnya.'],
                ['disabled' => count($superiors) == 0, 'selector' => '.tg-steps-outwork-approvers', 'title' => 'Persetujuan', 'content' => 'Pengajuan kegiatan yang kamu buat akan dicek sama atasan yang kamu pilih.'],
            ],
            fn($step) => !($step['disabled'] ?? false))),
])

@section('contents')
    {{-- Header Topbar --}}
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="" class="logo logo-dark">
                        <span class="logo-sm"><img src="{{ asset('skote/images/logo.svg') }}" height="22"></span>
                        <span class="logo-lg"><img src="{{ asset('skote/images/logo-dark.png') }}" height="17"></span>
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

    <style>
        .form-label-custom { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05rem; color: #495057; font-weight: 700; }
        .category-scroll { max-height: 300px; overflow-y: auto; border: 1px solid #eff2f7; border-radius: 8px; background: #fff; }
        .category-item { border-bottom: 1px solid #f3f3f9; transition: all 0.2s ease; cursor: pointer; }
        .category-item:hover { background-color: #f8f9fa; }
        .divider-vertical { border-right: 1px solid #eff2f7; }
        @media (max-width: 991px) { .divider-vertical { border-right: none; border-bottom: 1px solid #eff2f7; margin-bottom: 1.5rem; padding-bottom: 1rem; } }
        .date-row-card { background-color: #f8f9fa; border: 1px solid #eff2f7; border-radius: 8px; }
    </style>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row align-items-center mb-4 mt-2">
                    @include('layouts.component.alert-access')

                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::outwork.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Buat Pengajuan Insentif</h4>
                                <p class="text-muted mb-0 font-size-13">Laporkan kegiatan tambahan untuk klaim insentif.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4 p-md-5">
                                <form class="form-confirm form-block" action="{{ route('portal::outwork.submission.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    {{-- Section 1: Kategori --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary"><i class="mdi mdi-shape-outline me-1"></i> Bentuk Kegiatan</label>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <div class="tg-steps-outwork-category">
                                                <div class="category-scroll border shadow-sm">
                                                    @foreach($categories as $category => $children)
                                                        <div class="bg-light p-2 ps-3 text-muted fw-bold font-size-10 text-uppercase">{{ $category }}</div>
                                                        @foreach ($children as $child)
                                                            <label class="category-item d-flex align-items-center p-3 mb-0">
                                                                <input class="form-check-input" type="radio" name="ctg_id" value="{{ $child->id }}" required>
                                                                <span class="ms-2 text-dark fw-medium font-size-13">{{ ucfirst($child->description) }}</span>
                                                            </label>
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 2: Identitas --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary"><i class="mdi mdi-pencil-box-outline me-1"></i> Nama Kegiatan</label>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <div class="tg-steps-outwork-name">
                                                <input type="text" class="form-control" name="name" placeholder="Contoh: Panitia Pelaksana Wisuda 2024" required>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 3: Waktu --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary"><i class="mdi mdi-calendar-clock me-1"></i> Waktu Pelaksanaan</label>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <div id="dates" class="tg-steps-outwork-dates">
                                                <div class="date-row-card position-relative mb-3 p-3">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="font-size-11 fw-bold text-muted mb-1">TANGGAL</label>
                                                            <input type="date" class="form-control form-control-sm" name="dates[d][]" required>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="font-size-11 fw-bold text-muted mb-1">JAM OPERASIONAL</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="time" class="form-control" name="dates[s][]" required>
                                                                <span class="input-group-text bg-light">s.d</span>
                                                                <input type="time" class="form-control" name="dates[e][]" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="font-size-11 fw-bold text-muted mb-1">ISTIRAHAT (MENIT)</label>
                                                            <input type="number" class="form-control form-control-sm" name="dates[b][]" value="0" required>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-sm rounded-circle btn-add position-absolute shadow" style="right: -12px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; padding: 0;"><i class="mdi mdi-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 4: Lampiran --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary"><i class="mdi mdi-attachment me-1"></i> Dokumentasi</label>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <textarea class="form-control mb-3 tg-steps-outwork-description" name="description" rows="3" placeholder="Detail pekerjaan..."></textarea>
                                            <div class="tg-steps-outwork-attachment">
                                                <input class="form-control form-control-sm" name="file" type="file">
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-light">

                                    {{-- Section 5: Approvers (Sesuai Controller Dinamis) --}}
                                    <div class="row">
                                        <div class="col-lg-3 divider-vertical">
                                            <label class="form-label-custom d-block mb-2 text-primary">
                                                <i class="mdi mdi-account-check-outline me-1"></i> Verifikasi Atasan
                                            </label>
                                        </div>
                                        <div class="col-lg-9 ps-lg-4">
                                            <div class="row">
                                                @foreach ($superiors as $superior)
                                                    <div class="col-md-6 mb-3">
                                                        {{-- Label dihapus, pakai info Level saja --}}
                                                        <label class="font-size-11 fw-bold text-muted text-uppercase mb-1">Penanggungjawab Lv. {{ $superior['level_value'] }}</label>
                                                        <div class="tg-steps-outwork-approvers">
                                                            <select class="form-select" name="approvables[]" required>
                                                                @if ($superior['positions']->count() > 1)
                                                                    <option value="">-- Pilih Atasan --</option>
                                                                @endif
                                                                @foreach ($superior['positions'] as $empPos)
                                                                    <option value="{{ $empPos->id }}" @selected($superior['positions']->count() == 1)>
                                                                        {{ $empPos->employee->user->name }} - {{ $empPos->position->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-lg-9 offset-lg-3 ps-lg-4">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-lg px-5 waves-effect waves-light">Kirim Laporan</button>
                                                <a href="{{ request('next', route('portal::outwork.submission.index')) }}" class="btn btn-light btn-lg px-4">Batal</a>
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

    {{-- Template Row JS --}}
    <template id="dates-template">
        <div class="date-row-card position-relative mb-3 p-3">
            <div class="row g-3">
                <div class="col-md-4"><input type="date" class="form-control form-control-sm" name="dates[d][]" required></div>
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <input type="time" class="form-control" name="dates[s][]" required>
                        <span class="input-group-text">s.d</span>
                        <input type="time" class="form-control" name="dates[e][]" required>
                    </div>
                </div>
                <div class="col-md-3"><input type="number" class="form-control form-control-sm" name="dates[b][]" value="0" required></div>
            </div>
            <button type="button" class="btn btn-danger btn-sm rounded-circle btn-remove position-absolute shadow" style="right: -12px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; padding: 0;" onclick="removeDateRow(this)"><i class="mdi mdi-minus"></i></button>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        const MAX_DATES = 5;
        const removeDateRow = (btn) => { btn.closest('.date-row-card').remove(); checkAddButton(); }
        const checkAddButton = () => {
            const count = document.querySelectorAll('.date-row-card').length;
            const addBtn = document.querySelector('.btn-add');
            if (addBtn) addBtn.style.display = count >= MAX_DATES ? 'none' : 'flex';
        }
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-add')) {
                const datesContainer = document.getElementById('dates');
                if (document.querySelectorAll('.date-row-card').length < MAX_DATES) {
                    const clone = document.getElementById('dates-template').content.cloneNode(true);
                    datesContainer.appendChild(clone);
                }
                checkAddButton();
            }
        });
    </script>
@endpush

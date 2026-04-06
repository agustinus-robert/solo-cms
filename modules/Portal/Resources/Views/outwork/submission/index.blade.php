@extends('portal::layouts.index')

@section('title', 'Kegiatan Lainnya | ' . env('APP_NAME'))

@section('navtitle', 'Insentif')

@include('components.tourguide', [
    'steps' => array_values(
        array_filter(
            [
                [
                    'selector' => '.tg-steps-outwork-submission',
                    'title' => 'Pengajuan kegiatan lainnya',
                    'content' => 'Tekan tombol ini untuk melakukan pengajuan kegiatan lainnya.',
                ],
                [
                    'disabled' => !$approvers->contains($employee->position->id),
                    'selector' => '.tg-steps-outwork-manage',
                    'title' => 'Kelola kegiatan lainnya',
                    'content' => 'Silakan akses menu ini buat mengelola pengajuan kegiatan lainnya karyawan.',
                ],
                [
                    'selector' => '.tg-steps-outwork-filter',
                    'title' => 'Filter riwayat kegiatan lainnya',
                    'content' => 'Gunakan filter ini untuk melihat riwayat kegiatan lainnya pada bulan-bulan sebelumnya.',
                ],
                [
                    'selector' => '.tg-steps-outwork-table',
                    'title' => 'Tabel riwayat kegiatan lainnya',
                    'content' => 'Menampilkan riwayat kegiatan lainnya berdasarkan filter yang diterapkan.',
                ],
            ],
            fn($step) => !($step['disabled'] ?? false))),
])

@section('contents')
    {{-- Header Topbar --}}
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="index.html" class="logo logo-dark">
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
        .card-soft {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .badge-date {
            background-color: #f3f3f9;
            color: #495057;
            font-weight: 500;
            border-radius: 4px;
            padding: 3px 8px;
            display: inline-block;
        }
        .timeline-step-sm {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Header & Title --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Insentif Kegiatan</h4>
                                <p class="text-muted mb-0 font-size-13">Pantau laporan kegiatan dan status insentif Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alert Notification --}}
                @if (Session::has('success') || Session::has('danger'))
                    <div class="alert {{ Session::has('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi {{ Session::has('success') ? 'mdi-check-circle' : 'mdi-block-helper' }} me-2"></i>
                        {{ Session::get('success') ?? Session::get('danger') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Left Sidebar: Actions --}}
                    <div class="col-xl-3">
                        <div class="card card-soft tg-steps-outwork-submission overflow-hidden mb-4">
                            <div class="card-body text-center p-4">
                                <div class="avatar-md mx-auto mb-3">
                                    <span class="avatar-title rounded-circle text-white font-size-24 shadow-sm">
                                        <i class="mdi mdi-plus-circle-outline"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold font-size-15">Pengajuan Baru</h5>
                                <p class="text-muted font-size-12 mb-3">Laporkan kegiatan tambahan untuk mendapatkan insentif.</p>
                                <a href="{{ route('portal::outwork.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary btn-sm w-100 waves-effect waves-light">
                                    Buat Laporan
                                </a>
                            </div>
                        </div>

                        {{-- Admin/Manager Access --}}
                        @if (isset($employee->position->position_id) && in_array($employee->position->position_id, [Modules\Core\Enums\PositionTypeEnum::KEPALASEKOLAH->value, Modules\Core\Enums\PositionTypeEnum::HUMAS->value], true))
                            @if ($approvers->contains($employee->position->id))
                                <div class="tg-steps-outwork-manage mb-4">
                                    <label class="text-muted font-size-11 fw-bold text-uppercase mb-2 d-block">Manajemen</label>
                                    <a href="{{ route('portal::outwork.manage.index', ['next' => url()->current()]) }}" class="card card-soft border mb-0 waves-effect">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded bg-soft-info text-info">
                                                    <i class="mdi mdi-clipboard-check-multiple-outline"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 font-size-13 fw-bold">Kelola Insentif</h6>
                                                <small class="text-muted">Verifikasi laporan staf</small>
                                            </div>
                                            <i class="mdi mdi-chevron-right text-muted"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Right Content: Table History --}}
                    <div class="col-xl-9">
                        <div class="card card-soft shadow-sm border-0">
                            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0 fw-bold"><i class="mdi mdi-history me-1 text-primary"></i> Riwayat Kegiatan</h5>
                                <button class="btn btn-sm btn-light waves-effect border" data-bs-toggle="collapse" data-bs-target="#collapse-filter">
                                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                                </button>
                            </div>

                            {{-- Filter Form --}}
                            <div class="collapse @if (request('search')) show @endif" id="collapse-filter">
                                <div class="card-body bg-light bg-opacity-25 border-bottom">
                                    <form action="{{ route('portal::outwork.submission.index') }}" method="get" class="row g-3">
                                        <div class="col-md-5">
                                            <label class="form-label font-size-12 fw-bold text-muted">CARI KEGIATAN</label>
                                            <input class="form-control form-control-sm" type="search" name="search" placeholder="Nama kegiatan..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label font-size-12 fw-bold text-muted">PERIODE TANGGAL</label>
                                            <div class="input-group input-group-sm">
                                                <input class="form-control" type="date" name="start_at" value="{{ request('start_at') }}">
                                                <span class="input-group-text">s/d</span>
                                                <input class="form-control" type="date" name="end_at" value="{{ request('end_at') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="mdi mdi-magnify"></i></button>
                                            <a class="btn btn-light btn-sm" href="{{ route('portal::outwork.submission.index') }}"><i class="mdi mdi-refresh"></i></a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Table --}}
                            <div class="table-responsive tg-steps-outwork-table">
                                <table class="table align-middle table-nowrap table-hover mb-0">
                                    <thead class="table-light">
                                        <tr class="font-size-11 fw-bold text-muted text-uppercase">
                                            <th class="ps-4">Kegiatan & Kategori</th>
                                            <th>Waktu Pelaksanaan</th>
                                            <th class="text-center">Lampiran</th>
                                            <th class="text-center">Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($outworks as $outwork)
                                            <tr @class(['opacity-50' => $outwork->trashed()])>
                                                <td class="ps-4">
                                                    <h6 class="font-size-14 mb-1 fw-bold text-dark">{{ $outwork->name }}</h6>
                                                    <p class="text-muted font-size-11 mb-0">{{ $outwork->category->name }}</p>
                                                    <small class="text-primary font-size-10">Diajukan: {{ $outwork->created_at->format('d/m/Y') }}</small>
                                                </td>
                                                <td>
                                                    @foreach ($outwork->dates->take(2) as $date)
                                                        <div class="badge-date font-size-11 mb-1">
                                                            <i class="mdi mdi-calendar-clock me-1"></i>
                                                            {{ date('d M Y', strtotime($date['d'])) }}
                                                            @isset($date['t_s']) <small class="text-muted">({{ $date['t_s'] }})</small> @endisset
                                                        </div>
                                                    @endforeach
                                                    @if ($outwork->dates->count() > 2)
                                                        <small class="text-muted d-block font-size-10">+{{ $outwork->dates->count() - 2 }} hari lagi</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (isset($outwork->attachment) && Storage::exists($outwork->attachment))
                                                        <a href="{{ Storage::url($outwork->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info">
                                                            <i class="mdi mdi-paperclip font-size-14"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted font-size-18">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @include('portal::outwork.components.status', ['outwork' => $outwork])
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-vertical font-size-18"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                            <li><a class="dropdown-item" href="{{ route('portal::outwork.submission.show', ['outwork' => $outwork->id, 'next' => request('next')]) }}"><i class="mdi mdi-eye-outline me-2 text-primary"></i> Detail</a></li>
                                                            @if($outwork->hasApprovables() && !$outwork->trashed())
                                                                <li><a class="dropdown-item" href="javascript:;" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $outwork->id }}"><i class="mdi mdi-timeline-text-outline me-2 text-warning"></i> Lacak Status</a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Approval Tracking --}}
                                            @if ($outwork->hasApprovables() && !$outwork->trashed())
                                                <tr class="collapse @if ($outwork->hasAnyApprovableResultIn('PENDING')) show @endif" id="collapse-{{ $outwork->id }}">
                                                    <td colspan="5" class="p-0 border-0">
                                                        <div class="bg-light bg-opacity-50 p-4 border-start border-4 border-warning ms-4 my-2 rounded-3">
                                                            <div class="row gx-2">
                                                                @foreach ($outwork->approvables as $approvable)
                                                                    <div class="col-md-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <div class="timeline-step-sm me-2">
                                                                                <i class="mdi {{ $approvable->result->icon() }} font-size-12 text-{{ $approvable->result->color() }}"></i>
                                                                            </div>
                                                                            <div>
                                                                                <p class="mb-0 font-size-11 fw-bold text-dark">{{ ucfirst($approvable->type) }} Lv.{{ $approvable->level }}</p>
                                                                                <small class="text-muted font-size-10">{{ $approvable->userable ? $approvable->userable->getApproverLabel() : 'Menunggu...' }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr><td colspan="5" class="text-center py-5">@include('components.notfound')</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer bg-transparent py-3 border-top">
                                {{ $outworks->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script src="{{ asset('js/vendor/daterangepicker.js') }}"></script>
@endpush

@extends('portal::layouts.index')

@section('title', 'Lembur | ' . env('APP_NAME'))

@section('navtitle', 'Lembur')

@include('components.tourguide', [
    'steps' => array_values(
        array_filter(
            [
                [
                    'selector' => '.tg-steps-overtime-submission',
                    'title' => 'Pengajuan lembur',
                    'content' => 'Tekan tombol ini untuk melakukan pengajuan lembur.',
                ],
                [
                    'disabled' => !$approvers->contains($employee->position->id),
                    'selector' => '.tg-steps-overtime-manage',
                    'title' => 'Kelola lembur',
                    'content' => 'Silakan akses menu ini buat mengelola pengajuan lembur karyawan.',
                ],
                [
                    'selector' => '.tg-steps-overtime-filter',
                    'title' => 'Filter riwayat lembur',
                    'content' => 'Gunakan filter ini untuk melihat riwayat lembur pada bulan-bulan sebelumnya.',
                ],
                [
                    'selector' => '.tg-steps-overtime-table',
                    'title' => 'Tabel riwayat lembur',
                    'content' => 'Menampilkan riwayat lembur berdasarkan filter yang diterapkan.',
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
                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm"><img src="{{ asset('skote/images/logo-light.svg') }}" height="22"></span>
                        <span class="logo-lg"><img src="{{ asset('skote/images/logo-light.png') }}" height="39"></span>
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

                {{-- Breadcrumb & Header --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Manajemen Lembur</h4>
                                <p class="text-muted mb-0 font-size-13">Pantau dan ajukan jam kerja lembur Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <div class="tg-steps-overtime-submission">
                            <a href="{{ route('portal::overtime.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary waves-effect waves-light px-4">
                                <i class="mdi mdi-plus me-1"></i> Ajukan Lembur
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Sisi Kiri: Menu & Statistik --}}
                    <div class="col-xl-4">
                        {{-- Pusat Approval (Khusus Atasan) --}}
                        @if (isset($employee->position->position_id) && in_array($employee->position->position_id, [\Modules\Core\Enums\PositionTypeEnum::KEPALASEKOLAH->value, \Modules\Core\Enums\PositionTypeEnum::HUMAS->value], true))
                            @if ($approvers->contains($employee->position->id))
                                <div class="card bg-dark border-dark text-light shadow-sm mb-4 tg-steps-overtime-manage overflow-hidden position-relative">
                                    <div class="card-body position-relative" style="z-index: 1">
                                        <h5 class="text-white mb-2 font-size-15"><i class="mdi mdi-shield-check-outline me-1"></i> Verifikasi Lembur</h5>
                                        <p class="text-white-50 font-size-12">Terdapat pengajuan dari staf yang memerlukan persetujuan Anda.</p>
                                        <a href="{{ route('portal::overtime.manage.index', ['next' => url()->current()]) }}" class="btn btn-light btn-sm w-100 mt-2">Periksa Sekarang</a>
                                    </div>
                                    <i class="mdi mdi-clock-check-outline position-absolute text-white-50" style="right: -10px; bottom: -10px; font-size: 80px; opacity: 0.1;"></i>
                                </div>
                            @endif
                        @endif

                        {{-- Card Export --}}
                        <div class="card mini-stats-wid border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-2">Ekspor Data</p>
                                        <p class="text-muted font-size-12 mb-0">Unduh semua riwayat lembur dalam format Excel.</p>
                                    </div>
                                    <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-success">
                                        <span class="avatar-title pointer" onclick="exportExcel()">
                                            <i class="mdi mdi-file-excel font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                                <button onclick="exportExcel()" class="btn btn-soft-success btn-sm w-100 mt-3">Unduh (.xlsx)</button>
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Tabel Riwayat --}}
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body border-bottom bg-transparent py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="card-title mb-0"><i class="mdi mdi-history text-primary me-1"></i> Riwayat Lembur</h5>
                                    <button class="btn btn-sm btn-soft-secondary" data-bs-toggle="collapse" data-bs-target="#collapse-filter">
                                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                                    </button>
                                </div>

                                {{-- Filter Section --}}
                                <div class="collapse @if (request('search') || request('start_at')) show @endif" id="collapse-filter">
                                    <div class="pt-3 mt-3 border-top tg-steps-overtime-filter">
                                        <form action="{{ route('portal::overtime.submission.index') }}" method="get" class="row g-2">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control form-control-sm" name="search" placeholder="Cari kegiatan..." value="{{ request('search') }}">
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="date" class="form-control" name="start_at" value="{{ request('start_at') }}">
                                                    <span class="input-group-text">s/d</span>
                                                    <input type="date" class="form-control" name="end_at" value="{{ request('end_at') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Cari</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive tg-steps-overtime-table">
                                <table class="table table-centered table-nowrap mb-0 table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Kegiatan</th>
                                            <th>Jadwal Lembur</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($overtimes as $overtime)
                                            <tr class="{{ $overtime->trashed() ? 'opacity-50' : '' }}">
                                                <td class="ps-4">
                                                    <div>
                                                        <h6 class="text-truncate mb-1 font-size-14 text-dark">{{ $overtime->name }}</h6>
                                                        <p class="text-muted mb-0 font-size-11 text-truncate" style="max-width: 200px;">{{ $overtime->description }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php($items = $overtime->schedules ?? $overtime->dates ?? [])
                                                    @foreach (collect($items)->take(1) as $date)
                                                        <div class="font-size-12 fw-bold text-dark">
                                                            {{ date('d M Y', strtotime($date['d'])) }}
                                                        </div>
                                                        <div class="font-size-11 text-muted">
                                                            {{ $date['t_s'] ?? '??' }} - {{ $date['t_e'] ?? '??' }}
                                                        </div>
                                                    @endforeach
                                                    @if (count($items) > 1)
                                                        <span class="badge badge-soft-info font-size-10 mt-1">+{{ count($items)-1 }} hari lainnya</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @include('portal::overtime.components.status', ['overtime' => $overtime])
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-horizontal font-size-18 text-muted"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                                            <li><a class="dropdown-item py-2" href="{{ route('portal::overtime.submission.show', ['overtime' => $overtime->id, 'next' => request('next')]) }}">
                                                                <i class="mdi mdi-eye-outline text-primary me-2"></i> Detail</a>
                                                            </li>
                                                            @if($overtime->hasApprovables() && !$overtime->trashed())
                                                                <li><a class="dropdown-item py-2" href="javascript:;" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $overtime->id }}">
                                                                    <i class="mdi mdi-timeline-text-outline text-warning me-2"></i> Lacak Status</a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Tracking Timeline --}}
                                            @if ($overtime->hasApprovables() && !$overtime->trashed())
                                                <tr class="collapse @if ($overtime->hasAnyApprovableResultIn('PENDING')) show @endif" id="collapse-{{ $overtime->id }}">
                                                    <td colspan="4" class="bg-light bg-opacity-50 py-3 px-5">
                                                        <div class="ms-2 border-start border-2 border-light ps-4">
                                                            @foreach ($overtime->approvables as $approvable)
                                                                <div class="mb-3 position-relative">
                                                                    <i class="mdi {{ $approvable->result->name == 'APPROVE' ? 'mdi-check-circle text-success' : ($approvable->result->name == 'REJECT' ? 'mdi-close-circle text-danger' : 'mdi-clock-outline text-warning') }} position-absolute" style="left: -33px; font-size: 18px; background: #fff;"></i>
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <h6 class="font-size-13 mb-0">{{ $approvable->userable->getApproverLabel() }}</h6>
                                                                            <p class="text-muted mb-0 font-size-11">{{ ucfirst($approvable->type) }} Level {{ $approvable->level }}</p>
                                                                        </div>
                                                                        <span class="badge badge-soft-{{ $approvable->result->color() }} font-size-10">{{ $approvable->result->name }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <p class="text-muted mb-0">Belum ada riwayat lembur ditemukan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($overtimes->hasPages())
                                <div class="card-footer bg-transparent border-top">
                                    <div class="d-flex justify-content-center">
                                        {{ $overtimes->appends(request()->all())->links() }}
                                    </div>
                                </div>
                            @endif
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
    <script src="{{ asset('vendor/excel/excel.min.js') }}"></script>
    @include('portal::overtime.components.excel-script')
@endpush

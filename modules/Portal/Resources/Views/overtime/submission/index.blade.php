@extends('portal::layouts.index')

@section('title', 'Lembur | ' . env('APP_NAME'))

@section('navtitle', 'Lembur')

@include('components.tourguide', [
    'steps' => array_values(
        array_filter(
            [
                ['selector' => '.tg-steps-overtime-submission', 'title' => 'Pengajuan lembur', 'content' => 'Tekan tombol ini untuk melakukan pengajuan lembur.'],
                ['disabled' => !$isApprover, 'selector' => '.tg-steps-overtime-manage', 'title' => 'Kelola lembur', 'content' => 'Silakan akses menu ini buat mengelola pengajuan lembur karyawan.'],
                ['selector' => '.tg-steps-overtime-filter', 'title' => 'Filter riwayat lembur', 'content' => 'Gunakan filter ini untuk melihat riwayat lembur pada bulan-bulan sebelumnya.'],
                ['selector' => '.tg-steps-overtime-table', 'title' => 'Tabel riwayat lembur', 'content' => 'Menampilkan riwayat lembur berdasarkan filter yang diterapkan.'],
            ],
            fn($step) => !($step['disabled'] ?? false))),
])

@section('contents')
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

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Manajemen Lembur</h4>
                                <p class="text-muted mb-0 font-size-13">Pantau dan ajukan jam kerja lembur.</p>
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
                    <div class="col-xl-4">
                        {{-- Tab Switcher --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-2">
                                <div class="nav flex-column nav-pills">
                                    <a class="nav-link mb-1 {{ $view == 'mine' ? 'active' : '' }}" href="{{ route('portal::overtime.submission.index', ['view' => 'mine']) }}">
                                        <i class="mdi mdi-account-circle-outline me-2"></i> Pengajuan Saya
                                    </a>
                                    @if($isApprover || auth()->user()->hasRole('administrator'))
                                    <a class="nav-link {{ $view == 'approvals' ? 'active' : '' }} tg-steps-overtime-manage" href="{{ route('portal::overtime.submission.index', ['view' => 'approvals']) }}">
                                        <i class="mdi mdi-account-group-outline me-2"></i> Persetujuan Masuk
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Card Export --}}
                        <div class="card mini-stats-wid border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-sm align-self-center rounded-circle bg-success me-3">
                                        <span class="avatar-title bg-transparent"><i class="mdi mdi-file-excel font-size-24"></i></span>
                                    </div>
                                    <div>
                                        <p class="text-muted fw-medium mb-0">Ekspor Laporan</p>
                                        <small class="text-muted">Format Excel (.xlsx)</small>
                                    </div>
                                </div>
                                <button onclick="exportExcel()" class="btn btn-soft-success btn-sm w-100">Unduh Data</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body border-bottom bg-transparent py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="card-title mb-0">
                                        <i class="mdi mdi-history text-primary me-1"></i>
                                        {{ $view == 'approvals' ? 'Daftar Lembur Bawahan' : 'Riwayat Lembur Saya' }}
                                    </h5>
                                    <button class="btn btn-sm btn-soft-secondary" data-bs-toggle="collapse" data-bs-target="#collapse-filter">
                                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                                    </button>
                                </div>

                                <div class="collapse @if (request('search') || request('start_at')) show @endif" id="collapse-filter">
                                    <div class="pt-3 mt-3 border-top tg-steps-overtime-filter">
                                        <form action="{{ route('portal::overtime.submission.index') }}" method="get" class="row g-2">
                                            <input type="hidden" name="view" value="{{ $view }}">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control form-control-sm" name="search" placeholder="Cari kegiatan..." value="{{ request('search') }}">
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group input-group-sm">
                                                    <input type="date" class="form-control" name="start_at" value="{{ $start_at }}">
                                                    <input type="date" class="form-control" name="end_at" value="{{ $end_at }}">
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
                                            <th class="ps-4">Karyawan / Kegiatan</th>
                                            <th>Jadwal</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($overtimes as $overtime)
                                            <tr class="{{ $overtime->trashed() ? 'opacity-50' : '' }}">
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        @if($view == 'approvals' || auth()->user()->hasRole('administrator'))
                                                            <div class="avatar-xs me-3">
                                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12 fw-bold">
                                                                    {{ substr($overtime->employee->user->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="text-truncate mb-1 font-size-14 text-dark fw-bold">{{ $overtime->name }}</h6>
                                                            <p class="text-muted mb-0 font-size-11">
                                                                <i class="mdi mdi-account-circle-outline"></i> {{ $overtime->employee->user->name }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php($items = $overtime->schedules ?? $overtime->dates ?? [])
                                                    @foreach (collect($items)->take(1) as $date)
                                                        <div class="font-size-12 fw-bold text-dark">{{ date('d M Y', strtotime($date['d'])) }}</div>
                                                        <div class="font-size-11 text-muted">{{ $date['t_s'] }} - {{ $date['t_e'] ?? '??' }}</div>
                                                    @endforeach
                                                    @if (count($items) > 1)
                                                        <span class="badge badge-soft-info font-size-10">+{{ count($items)-1 }} hari</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">@include('portal::overtime.components.status', ['overtime' => $overtime])</td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('portal::overtime.submission.show', ['overtime' => $overtime->id, 'next' => url()->full()]) }}" class="btn btn-sm btn-soft-primary">
                                                        <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat lembur ditemukan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($overtimes->hasPages())
                                <div class="card-footer bg-transparent border-top">{{ $overtimes->appends(request()->all())->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

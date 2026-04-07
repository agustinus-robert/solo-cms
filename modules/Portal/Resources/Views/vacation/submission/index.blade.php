@extends('portal::layouts.index')

@section('title', 'Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

@include('components.tourguide', [
    'steps' => array_values(
        array_filter(
            [
                ['selector' => '.tg-steps-vacation-submission', 'title' => 'Pengajuan Cuti', 'content' => 'Tekan tombol ini untuk melakukan pengajuan cuti baru.'],
                ['disabled' => !$isApprover, 'selector' => '.tg-steps-vacation-manage', 'title' => 'Persetujuan Masuk', 'content' => 'Kelola pengajuan cuti dari bawahan Anda di sini.'],
                ['selector' => '.tg-steps-vacation-filter', 'title' => 'Filter Data', 'content' => 'Cari riwayat cuti berdasarkan kategori atau tanggal.'],
                ['selector' => '.tg-steps-vacation-table', 'title' => 'Daftar Pengajuan', 'content' => 'Menampilkan daftar cuti sesuai status dan filter.'],
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
                                <h4 class="mb-0 fw-bold text-dark">Manajemen Cuti</h4>
                                <p class="text-muted mb-0 font-size-13">Kelola jatah dan pantau pengajuan cuti.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-body p-2">
                                <div class="nav flex-column nav-pills">
                                    <a class="nav-link mb-1 {{ $view == 'mine' ? 'active' : '' }}" href="{{ route('portal::vacation.submission.index', ['view' => 'mine']) }}">
                                        <i class="mdi mdi-account-circle-outline me-2"></i> Pengajuan Saya
                                    </a>
                                    @if($isApprover || auth()->user()->hasRole('administrator'))
                                    <a class="nav-link {{ $view == 'approvals' ? 'active' : '' }} tg-steps-vacation-manage" href="{{ route('portal::vacation.submission.index', ['view' => 'approvals']) }}">
                                        <i class="mdi mdi-account-group-outline me-2"></i> Persetujuan Masuk
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($view == 'mine')
                            <div class="card border-0 shadow-sm mb-4 tg-steps-vacation-submission overflow-hidden" style="border-radius: 12px;">
                                <div class="card-body py-4 text-center">
                                    <div class="avatar-md mx-auto mb-3">
                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                            <i class="mdi mdi-calendar-plus font-size-24"></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-bold">Ajukan Cuti</h5>
                                    <p class="text-muted font-size-13 mb-4">Butuh waktu istirahat? Klik tombol di bawah untuk memulai pengajuan.</p>

                                    @if (count($quotas))
                                        <a href="{{ route('portal::vacation.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary w-100 py-2 waves-effect waves-light">
                                            <i class="mdi mdi-airplane-takeoff me-1"></i> Mulai Pengajuan
                                        </a>
                                    @else
                                        <button class="btn btn-secondary disabled w-100 py-2" title="Anda belum memiliki kuota cuti aktif">
                                            <i class="mdi mdi-lock me-1"></i> Kuota Belum Tersedia
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Detail Kuota tetap muncul di bawahnya --}}
                            <div class="card border-0 shadow-sm mb-4 tg-steps-vacation-quota" style="border-radius: 12px;">
                                <div class="card-header bg-transparent border-bottom">
                                    <h6 class="mb-0 fw-bold text-primary"><i class="mdi mdi-chart-donut me-1"></i> Sisa Jatah Cuti</h6>
                                </div>
                                <div class="card-body p-0">
                                    @forelse($quotas as $quota)
                                        <div class="d-flex align-items-center p-3 border-bottom border-light">
                                            <div class="flex-grow-1">
                                                <span class="badge badge-soft-info text-uppercase font-size-10 mb-1">{{ $quota->category->name }}</span>
                                                <p class="text-muted font-size-11 mb-0 italic">S.d {{ $quota->end_at->translatedFormat('d M Y') }}</p>
                                            </div>
                                            <div class="text-end">
                                                <h4 class="mb-0 fw-bolder text-dark">{{ $quota->remain }}</h4>
                                                <small class="text-muted font-size-10 text-uppercase">Hari</small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted font-size-12">
                                            <i class="mdi mdi-calendar-remove d-block font-size-20 mb-1"></i>
                                            Belum ada jatah cuti aktif.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="mdi mdi-history me-1"></i>
                                    {{ $view == 'approvals' ? 'Daftar Cuti Bawahan' : 'Riwayat Pengajuan' }}
                                </h6>
                                <button class="btn btn-sm btn-soft-secondary font-size-11" type="button" data-bs-toggle="collapse" data-bs-target="#filter-collapse">
                                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                                </button>
                            </div>

                            <div class="collapse @if (request('search') || request('start_at')) show @endif tg-steps-vacation-filter" id="filter-collapse">
                                <div class="card-body bg-light bg-opacity-25 border-bottom">
                                    <form action="{{ route('portal::vacation.submission.index') }}" method="get" class="row g-2">
                                        <input type="hidden" name="view" value="{{ $view }}">
                                        <div class="col-md-5">
                                            <input class="form-control form-control-sm" type="search" name="search" placeholder="Cari keterangan..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control" type="date" name="start_at" value="{{ $start_at }}">
                                                <input class="form-control" type="date" name="end_at" value="{{ $end_at }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive tg-steps-vacation-table">
                                <table class="table table-centered table-nowrap align-middle mb-0 table-hover">
                                    <thead class="table-light font-size-11 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4">Karyawan / Kategori</th>
                                            <th>Jadwal Cuti</th>
                                            <th class="text-center">Status</th>
                                            <th class="pe-4 text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vacations as $vacation)
                                            <tr @class(['opacity-50' => $vacation->trashed()])>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        @if($view == 'approvals')
                                                            <div class="avatar-xs me-3">
                                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12 fw-bold">
                                                                    {{-- Perbaikan Jalur: Quota -> Employee -> User --}}
                                                                    {{ substr($vacation->quota->employee->user->name ?? '?', 0, 1) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="font-size-13 mb-1 text-dark fw-bold">{{ $vacation->quota->category->name ?? '-' }}</h6>
                                                            <p class="text-muted font-size-11 mb-0">
                                                                <i class="mdi mdi-account-circle-outline"></i>
                                                                {{ $vacation->quota->employee->user->name ?? 'Karyawan Tidak Ditemukan' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php($firstDate = collect($vacation->dates)->first())
                                                    @if($firstDate)
                                                        <div class="font-size-12 fw-medium text-dark">
                                                            {{ \Carbon\Carbon::parse($firstDate['d'])->translatedFormat('d M Y') }}
                                                        </div>
                                                        <small class="text-muted">Total: {{ count($vacation->dates) }} Hari</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @include('portal::vacation.components.status', ['vacation' => $vacation])
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <a class="btn btn-sm btn-soft-primary" href="{{ route('portal::vacation.submission.show', ['vacation' => $vacation->id, 'next' => url()->full()]) }}">
                                                            <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                        </a>

                                                        @if($view == 'mine' && $vacation->can('deleted'))
                                                        <form class="form-confirm" action="{{ route('portal::vacation.submission.destroy', ['vacation' => $vacation->id]) }}" method="post">
                                                            @csrf @method('delete')
                                                            <button type="submit" class="btn btn-sm btn-soft-danger"><i class="mdi mdi-trash-can-outline"></i></button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5">@include('components.notfound')</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($vacations->hasPages())
                                <div class="card-footer bg-transparent border-top py-3">
                                    {{ $vacations->appends(request()->all())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

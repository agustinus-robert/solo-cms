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
                    'disabled' => !$isApprover,
                    'selector' => '.tg-steps-outwork-manage',
                    'title' => 'Persetujuan Masuk',
                    'content' => 'Kelola pengajuan kegiatan dari bawahan Anda di sini.',
                ],
                [
                    'selector' => '.tg-steps-outwork-filter',
                    'title' => 'Filter Data',
                    'content' => 'Gunakan filter ini untuk mencari riwayat berdasarkan nama atau tanggal.',
                ],
                [
                    'selector' => '.tg-steps-outwork-table',
                    'title' => 'Daftar Kegiatan',
                    'content' => 'Menampilkan daftar laporan sesuai status dan filter.',
                ],
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
                    @include('layouts.component.alert-access')

                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Insentif Kegiatan</h4>
                                <p class="text-muted mb-0 font-size-13">Kelola laporan kegiatan luar dan pantau status insentif.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-check-circle me-2"></i> {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Navigasi View --}}
                    <div class="col-xl-3">
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-body p-2">
                                <div class="nav flex-column nav-pills">
                                    <a class="nav-link mb-1 {{ $view == 'mine' ? 'active' : '' }}" href="{{ route('portal::outwork.submission.index', ['view' => 'mine']) }}">
                                        <i class="mdi mdi-account-circle-outline me-2"></i> Pengajuan Saya
                                    </a>
                                    @if($isApprover)
                                    <a class="nav-link {{ $view == 'approvals' ? 'active' : '' }} tg-steps-outwork-manage" href="{{ route('portal::outwork.submission.index', ['view' => 'approvals']) }}">
                                        <i class="mdi mdi-account-group-outline me-2"></i> Persetujuan Masuk
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($view == 'mine')
                        <div class="card border-0 shadow-sm tg-steps-outwork-submission overflow-hidden" style="border-radius: 12px;">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-md mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                        <i class="mdi mdi-file-plus-outline font-size-24"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold">Lapor Kegiatan</h5>
                                <p class="text-muted font-size-13 mb-4">Laporkan kegiatan baru untuk diproses insentifnya.</p>
                                <a href="{{ route('portal::outwork.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary w-100 py-2 waves-effect waves-light">
                                    <i class="mdi mdi-plus me-1"></i> Mulai Laporan
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Tabel Riwayat --}}
                    <div class="col-xl-9">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="mdi mdi-history me-1 text-primary"></i>
                                    {{ $view == 'approvals' ? 'Daftar Laporan Bawahan' : 'Riwayat Pengajuan' }}
                                </h6>
                                <button class="btn btn-sm btn-soft-secondary font-size-11" type="button" data-bs-toggle="collapse" data-bs-target="#filter-collapse">
                                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                                </button>
                            </div>

                            <div class="collapse @if (request('search') || request('start_at')) show @endif tg-steps-outwork-filter" id="filter-collapse">
                                <div class="card-body bg-light bg-opacity-25 border-bottom">
                                    <form action="{{ route('portal::outwork.submission.index') }}" method="get" class="row g-2">
                                        <input type="hidden" name="view" value="{{ $view }}">
                                        <div class="col-md-5">
                                            <input class="form-control form-control-sm" type="search" name="search" placeholder="Cari kegiatan..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control" type="date" name="start_at" value="{{ request('start_at') }}">
                                                <input class="form-control" type="date" name="end_at" value="{{ request('end_at') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive tg-steps-outwork-table">
                                <table class="table table-centered table-nowrap align-middle mb-0 table-hover">
                                    <thead class="table-light font-size-11 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4">Karyawan / Kegiatan</th>
                                            <th>Kategori</th>
                                            <th class="text-center">Status</th>
                                            <th class="pe-4 text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($outworks as $outwork)
                                            <tr @class(['opacity-50' => $outwork->trashed()])>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        @if($view == 'approvals')
                                                            <div class="avatar-xs me-3">
                                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-11 fw-bold">
                                                                    {{ substr($outwork->employee->user->name ?? '?', 0, 1) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="font-size-13 mb-1 text-dark fw-bold">{{ $outwork->name }}</h6>
                                                            <p class="text-muted font-size-11 mb-0">
                                                                <i class="mdi mdi-account-circle-outline"></i>
                                                                {{ $outwork->employee->user->name }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-soft-info text-uppercase font-size-10">{{ $outwork->category->name }}</span>
                                                    <div class="text-muted font-size-11 mt-1">{{ $outwork->created_at->format('d/m/Y') }}</div>
                                                </td>
                                                <td class="text-center">
                                                    @include('portal::outwork.components.status', ['outwork' => $outwork])
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <a class="btn btn-sm btn-soft-primary" href="{{ route('portal::outwork.submission.show', ['outwork' => $outwork->id, 'next' => url()->full()]) }}">
                                                            <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                        </a>

                                                        @if($view == 'mine' && $outwork->can('deleted'))
                                                        <form class="form-confirm" action="{{ route('portal::outwork.submission.destroy', ['outwork' => $outwork->id]) }}" method="post">
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
                            @if($outworks->hasPages())
                                <div class="card-footer bg-transparent border-top py-3">
                                    {{ $outworks->appends(request()->all())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

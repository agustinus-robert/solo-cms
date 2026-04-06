@extends('portal::layouts.index')

@section('title', 'Izin | ' . env('APP_NAME'))

@section('navtitle', 'Perizinan')

@include('components.tourguide', [
    'steps' => array_filter([
        ['selector' => '.tg-steps-leave-submission', 'title' => 'Pengajuan izin', 'content' => 'Tekan tombol ini untuk melakukan pengajuan izin.'],
        ['selector' => '.tg-steps-leave-count', 'title' => 'Statistik izin', 'content' => 'Kolom ini menampilkan statistik izin yang telah kamu gunakan di tahun ini.'],
        ['selector' => '.tg-steps-leave-filter', 'title' => 'Filter riwayat izin', 'content' => 'Gunakan filter ini untuk melihat riwayat izin pada bulan-bulan sebelumnya.'],
        ['selector' => '.tg-steps-leave-table', 'title' => 'Tabel riwayat izin', 'content' => 'Menampilkan riwayat izin berdasarkan filter yang diterapkan.'],
    ]),
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
        .mini-stats-wid .card-body { padding: 1.25rem; }
        .table-light-th th { background-color: #f8f9fa !important; border-bottom: 1px solid #eff2f7 !important; }
    </style>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Header Section --}}
                <div class="row align-items-center mb-4 mt-2">
                    @include('layouts.component.alert-access')

                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Manajemen Izin</h4>
                                <p class="text-muted mb-0 font-size-13">Kelola kehadiran dan pantau riwayat pengajuan izin Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <div class="tg-steps-leave-submission">
                            <a href="{{ route('portal::leave.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Buat Izin Baru
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="row">
                    <div class="col-xl-4 col-md-6">
                        <div class="card mini-stats-wid card-soft tg-steps-leave-count">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium font-size-12 mb-2 text-uppercase">Total Izin {{ date('Y') }}</p>
                                        <h4 class="mb-0 fw-bold">{{ $leaves_this_year_count }} <span class="font-size-13 fw-normal text-muted">Kali</span></h4>
                                    </div>
                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-soft-primary">
                                            <span class="avatar-title bg-soft-primary text-primary font-size-24 rounded-circle">
                                                <i class="mdi mdi-calendar-clock"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Admin Quick Access --}}
                 @php
                    $hasSubordinates = false;
                    if (isset($employee->position->position)) {
                        $hasSubordinates = $employee->position->position->children()->exists();
                    }

                @endphp

                @if ($hasSubordinates || auth()->user()->hasRole('administrator'))
                <div class="col-xl-8 col-md-6">
                    <div class="card bg-dark card-soft overflow-hidden">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-sm-8">
                                    <div class="text-white">
                                        <h5 class="text-white fw-bold mb-1">Pusat Persetujuan</h5>
                                        <p class="text-white-50 mb-0 font-size-12">
                                            @if(auth()->user()->hasRole('administrator'))
                                                Anda memiliki akses penuh untuk memvalidasi seluruh izin staf.
                                            @else
                                                Anda memiliki wewenang untuk memvalidasi izin staf di bawah koordinasi Anda.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                                    <a href="{{ route('portal::leave.manage.index', ['next' => url()->current()]) }}" class="btn btn-light btn-sm px-3 shadow-none">
                                        Periksa Sekarang <i class="mdi mdi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <i class="mdi mdi-shield-check-outline position-absolute text-white" style="right: -10px; bottom: -10px; font-size: 80px; opacity: 0.1;"></i>
                        </div>
                    </div>
                </div>
                @endif
                </div>

                {{-- Table History --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card card-soft shadow-sm">
                            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0 fw-bold"><i class="mdi mdi-history me-1 text-primary"></i> Riwayat Izin</h5>
                                <div>
                                    <button class="btn btn-sm btn-light waves-effect border" data-bs-toggle="collapse" data-bs-target="#collapse-filter">
                                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('portal::leave.submission.index') }}" class="btn btn-sm btn-light border ms-1"><i class="mdi mdi-refresh"></i></a>
                                </div>
                            </div>

                            {{-- Filter Section --}}
                            <div class="collapse @if (request('search') || request('start_at')) show @endif" id="collapse-filter">
                                <div class="card-body bg-light bg-opacity-25 border-bottom tg-steps-leave-filter">
                                    <form action="{{ route('portal::leave.submission.index') }}" method="get" class="row g-3">
                                        <div class="col-md-5">
                                            <label class="form-label font-size-11 fw-bold text-muted">CARI KEPERLUAN</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" name="search" placeholder="Contoh: Sakit, Urusan Keluarga..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label font-size-11 fw-bold text-muted">RENTANG TANGGAL</label>
                                            <div class="input-group input-group-sm">
                                                <input type="date" class="form-control" name="start_at" value="{{ request('start_at') }}">
                                                <span class="input-group-text">s/d</span>
                                                <input type="date" class="form-control" name="end_at" value="{{ request('end_at') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive tg-steps-leave-table">
                                <table class="table align-middle table-nowrap table-hover mb-0">
                                    <thead class="table-light-th">
                                        <tr class="font-size-11 fw-bold text-muted text-uppercase">
                                            <th class="ps-4">Tipe & Keperluan</th>
                                            <th>Periode / Durasi</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leaves as $leave)
                                            <tr @class(['opacity-50' => $leave->trashed()])>
                                                <td class="ps-4">
                                                    <h6 class="font-size-14 mb-1 fw-bold text-dark">{{ $leave->category->name }}</h6>
                                                    <p class="text-muted font-size-11 mb-0">{{ Str::limit($leave->description, 50) }}</p>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <span class="avatar-title rounded bg-soft-info text-info font-size-10">
                                                                <i class="mdi mdi-calendar"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="font-size-12 mb-0">{{ $leave->created_at->translatedFormat('d M Y') }}</h6>
                                                            <small class="text-muted">{{ count($leave->dates) }} Hari Izin</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @include('portal::leave.components.status', ['leave' => $leave])
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle card-drop shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-horizontal font-size-20 text-muted"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                                                            <li><a class="dropdown-item py-2" href="{{ route('portal::leave.submission.show', ['leave' => $leave->id]) }}">
                                                                <i class="mdi mdi-eye-outline me-2 text-primary font-size-16"></i> Detail & Dokumen</a>
                                                            </li>
                                                            @if($leave->hasApprovables())
                                                                <li><a class="dropdown-item py-2" href="javascript:;" data-bs-toggle="collapse" data-bs-target="#track-{{ $leave->id }}">
                                                                    <i class="mdi mdi-timeline-clock-outline me-2 text-info font-size-16"></i> Lacak Persetujuan</a>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item py-2 text-danger fw-medium d-flex align-items-center" href="#">
                                                                <i class="mdi mdi-close-circle-outline me-2 font-size-16"></i> Batalkan Izin</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Track Approval Style --}}
                                            @if ($leave->hasApprovables())
                                                <tr class="collapse border-0 bg-light bg-opacity-25" id="track-{{ $leave->id }}">
                                                    <td colspan="4" class="p-4 border-0">
                                                        <div class="px-3 border-start border-3 border-info">
                                                            <h6 class="font-size-11 text-uppercase fw-bold text-info mb-3">Timeline Persetujuan</h6>
                                                            <div class="row gx-2">
                                                                @foreach ($leave->approvables as $approvable)
                                                                    <div class="col-md-3 mb-2 mb-md-0">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="avatar-xs me-2">
                                                                                <span class="avatar-title rounded-circle bg-white shadow-sm font-size-12 text-{{ $approvable->result->color() }}">
                                                                                    <i class="mdi {{ $approvable->result->name == 'APPROVE' ? 'mdi-check-bold' : ($approvable->result->name == 'REJECT' ? 'mdi-close-thick' : 'mdi-timer-sand') }}"></i>
                                                                                </span>
                                                                            </div>
                                                                            <div>
                                                                                <p class="mb-0 font-size-11 fw-bold text-dark">{{ ucfirst($approvable->type) }} Lv.{{ $approvable->level }}</p>
                                                                                <small class="text-muted font-size-10">{{ $approvable->userable->getApproverLabel() }}</small>
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
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    @include('components.notfound')
                                                    <p class="text-muted mt-2">Belum ada data pengajuan izin yang tercatat.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($leaves->hasPages())
                            <div class="card-footer bg-transparent py-3 border-top">
                                <div class="d-flex justify-content-center">
                                    {{ $leaves->appends(request()->all())->links() }}
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

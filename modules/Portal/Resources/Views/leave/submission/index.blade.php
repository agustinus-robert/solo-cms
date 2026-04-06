@extends('portal::layouts.index')

@section('title', 'Izin | ')

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

                <div class="dropdown d-inline-block d-lg-none ms-2">
                    <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">

                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Search input">

                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

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
            <div class="container-fluid py-4">
                {{-- Header --}}
                <div class="row mb-4">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::dashboard-msdm.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="font-weight-bolder mb-0">Manajemen Izin</h4>
                                <p class="text-sm text-secondary mb-0">Kelola kehadiran dan pengajuan izin Anda.</p>
                            </div>
                        </div>
                        <div class="tg-steps-leave-submission">
                            <a href="{{ route('portal::leave.submission.create', ['next' => url()->full()]) }}" class="btn btn-primary d-flex align-items-center mb-0">
                                <span class="material-symbols-rounded me-2">add</span> Buat Izin
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Statistik --}}
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card tg-steps-leave-count border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon icon-shape bg-soft-primary text-primary border-radius-md shadow-none d-flex align-items-center justify-content-center me-3" style="background: #eef2ff">
                                                <span class="material-symbols-rounded">event_note</span>
                                            </div>
                                            <div>
                                                <span class="text-xs text-uppercase font-weight-bold text-secondary">Total Izin {{ date('Y') }}</span>
                                                <h5 class="font-weight-bolder mb-0">{{ $leaves_this_year_count }} <small class="text-secondary font-weight-normal text-xs">Kali</small></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Approval Cepat jika Atasan --}}
                            @php use Modules\Core\Enums\PositionTypeEnum; @endphp
                            @if (isset($employee->position->position_id) && in_array($employee->position->position_id, [PositionTypeEnum::KEPALASEKOLAH->value, PositionTypeEnum::HUMAS->value], true))
                            <div class="col-md-8 mb-3">
                                <div class="card border-0 shadow-sm bg-dark overflow-hidden position-relative">
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between position-relative" style="z-index: 1">
                                        <div class="d-flex align-items-center">
                                            <div class="icon icon-shape bg-white-20 text-white border-radius-md me-3 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.1)">
                                                <span class="material-symbols-rounded">rule</span>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-0">Pusat Persetujuan</h6>
                                                <p class="text-white text-xs opacity-7 mb-0">Anda memiliki hak akses untuk memvalidasi izin staf.</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('portal::leave.manage.index', ['next' => url()->current()]) }}" class="btn btn-sm btn-white mb-0 shadow-none">Periksa Sekarang</a>
                                    </div>
                                    <span class="material-symbols-rounded position-absolute text-white" style="right: -10px; top: -10px; font-size: 80px; opacity: 0.1;">verified</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Main Content --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h6 class="mb-0 font-weight-bolder"><span class="material-symbols-rounded me-1 text-primary">history</span> Riwayat Izin</h6>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button class="btn btn-link text-dark text-xs p-0 mb-0 me-3" data-bs-toggle="collapse" data-bs-target="#collapse-filter">
                                            <span class="material-symbols-rounded text-sm me-1">tune</span> Filter
                                        </button>
                                        <a href="{{ route('portal::leave.submission.index') }}" class="btn btn-link text-secondary text-xs p-0 mb-0">
                                            <span class="material-symbols-rounded text-sm">refresh</span>
                                        </a>
                                    </div>
                                </div>

                                {{-- Collapsible Filter --}}
                                <div class="collapse @if (request('search') || request('start_at')) show @endif" id="collapse-filter">
                                    <div class="mt-3 pt-3 border-top tg-steps-leave-filter">
                                        <form action="{{ route('portal::leave.submission.index') }}" method="get" class="row g-3">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline">
                                                    <label class="form-label">Cari keperluan...</label>
                                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="input-group input-group-outline">
                                                        <input type="date" class="form-control" name="start_at" value="{{ request('start_at') }}">
                                                    </div>
                                                    <span class="text-secondary">-</span>
                                                    <div class="input-group input-group-outline">
                                                        <input type="date" class="form-control" name="end_at" value="{{ request('end_at') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary w-100 mb-0">Terapkan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive tg-steps-leave-table">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Tipe & Keperluan</th>
                                            <th>Periode / Durasi</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leaves as $leave)
                                            <tr class="{{ $leave->trashed() ? 'opacity-5' : '' }}">
                                                <td class="ps-4">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-sm font-weight-bold text-dark">{{ $leave->category->name }}</span>
                                                        <span class="text-xs text-secondary">{{ Str::limit($leave->description, 40) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-xs font-weight-bold text-dark">{{ $leave->created_at->translatedFormat('d M Y') }}</span>
                                                        <span class="text-xxs text-secondary">{{ count($leave->dates) }} Hari Izin</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @include('portal::leave.components.status', ['leave' => $leave])
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown" id="action{{$leave->id}}">
                                                            <span class="material-symbols-rounded">more_horiz</span>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2">
                                                            <li><a class="dropdown-item py-2" href="{{ route('portal::leave.submission.show', ['leave' => $leave->id]) }}">
                                                                <span class="material-symbols-rounded text-sm me-2 text-primary">info</span> Detail & Dokumen</a>
                                                            </li>
                                                            @if($leave->hasApprovables())
                                                                <li><a class="dropdown-item py-2" href="javascript:;" data-bs-toggle="collapse" data-bs-target="#track-{{ $leave->id }}">
                                                                    <span class="material-symbols-rounded text-sm me-2 text-info">analytics</span> Lacak Persetujuan</a>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item py-2 text-danger d-flex align-items-center" href="#">
                                                                <span class="material-symbols-rounded text-sm me-2">cancel</span> Batalkan Izin</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Approval Tracking (NEW STYLE) --}}
                                            @if ($leave->hasApprovables())
                                                <tr class="collapse border-0 bg-light-soft" id="track-{{ $leave->id }}">
                                                    <td colspan="4" class="py-4 px-5 border-0">
                                                        <h6 class="text-xs text-uppercase font-weight-bolder mb-3">Timeline Persetujuan</h6>
                                                        <div class="ps-2">
                                                            @foreach ($leave->approvables as $approvable)
                                                                <div class="stepper-item {{ $approvable->result->name != 'PENDING' ? 'stepper-active' : '' }}">
                                                                    <div class="stepper-dot">
                                                                        <span class="material-symbols-rounded" style="font-size: 14px;">
                                                                            {{ $approvable->result->name == 'APPROVE' ? 'check' : ($approvable->result->name == 'REJECT' ? 'close' : 'schedule') }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <p class="text-xs font-weight-bold mb-0 text-dark">{{ ucfirst($approvable->type) }} Level {{ $approvable->level }}</p>
                                                                            <p class="text-xxs text-secondary mb-0">{{ $approvable->userable->getApproverLabel() }}</p>
                                                                        </div>
                                                                        <span class="badge badge-sm bg-soft-{{ $approvable->result->color() }} text-{{ $approvable->result->color() }} border-radius-pill py-1">
                                                                            {{ $approvable->result->name }}
                                                                        </span>
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
                                                    @include('components.notfound')
                                                    <p class="text-sm text-secondary">Belum ada data izin yang tercatat.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($leaves->hasPages())
                            <div class="card-footer py-3">
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

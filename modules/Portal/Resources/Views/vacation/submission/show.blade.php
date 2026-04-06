@extends('portal::layouts.index')

@section('title', 'Detail Pengajuan Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

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
                @php($user=auth()->user())
                @include('portal::layouts.components.notifications')
                @include('layouts.shortcut_menu')
                <div class="dropdown d-none d-lg-inline-block ms-1">
                    @include('layouts.nav_name')
                </div>
            </div>
        </div>
    </header>

    {{-- Horizontal Navbar --}}
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
                <div class="navbar-collapse collapse" id="topnav-menu-content">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link arrow-none" href="{{ route('portal::dashboard-msdm.index') }}" id="topnav-dashboard" role="button">
                                <i class="bx bx-home-circle me-2"></i><span key="t-dashboards">Dashboards</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Header Section --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::vacation.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Informasi Pengajuan Cuti</h4>
                                <p class="text-muted mb-0 font-size-13">Detail riwayat dan status pengajuan karyawan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($vacation->trashed())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian!</strong> Pengajuan ini telah dihapus. Anda tidak dapat lagi mengelola data ini.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h5 class="card-title mb-0 fw-bold text-primary">
                                        <i class="mdi mdi-text-box-search-outline me-1"></i> Rincian Pengajuan
                                    </h5>
                                    @if (!$vacation->trashed())
                                        <a class="btn btn-soft-success btn-sm waves-effect waves-light" href="{{ route('portal::vacation.print', ['vacation' => $vacation->id]) }}" target="_blank">
                                            <i class="mdi mdi-printer me-1"></i> Cetak PDF
                                        </a>
                                    @endif
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Tanggal Pengajuan</label>
                                        <p class="mb-0 text-dark fw-medium">{{ $vacation->created_at->formatLocalized('%A, %d %B %Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Kategori Cuti</label>
                                        <p class="mb-0 text-dark fw-medium">{{ $vacation->quota->category->name }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-2 d-block">Daftar Tanggal Cuti</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @isset(collect($vacation->dates)->first()['cashable'])
                                                <span class="badge bg-dark px-3 py-2 fw-medium text-white">{{ $vacation->dates->count() }} Hari dikompensasikan</span>
                                            @else
                                                @foreach ($vacation->dates as $date)
                                                    <span class="badge bg-soft-secondary text-dark px-3 py-2 fw-medium {{ isset($date['c']) ? 'text-decoration-line-through' : '' }}" @isset($date['f']) data-bs-toggle="tooltip" title="Freelance Mode" @endisset>
                                                        @isset($date['f'])
                                                            <i class="mdi mdi-account-network text-danger me-1"></i>
                                                        @endisset
                                                        {{ strftime('%d %B %Y', strtotime($date['d'])) }}
                                                    </span>
                                                @endforeach
                                            @endisset
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Alasan / Deskripsi</label>
                                        <p class="mb-0 text-dark">{{ $vacation->description ?: '-' }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-2 d-block">Status Saat Ini</label>
                                        <div>@include('portal::vacation.components.status', ['vacation' => $vacation])</div>
                                    </div>
                                </div>

                                @if ($vacation->approvables->count())
                                    <div class="mt-5">
                                        <h6 class="text-muted font-size-12 text-uppercase fw-bold mb-3">Alur Persetujuan</h6>
                                        <div class="table-responsive">
                                            <table class="table table-nowrap align-middle table-borderless bg-light rounded-3 overflow-hidden">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">Penanggungjawab</th>
                                                        <th>Level</th>
                                                        <th>Keputusan</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($vacation->approvables as $approvable)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <span class="d-block fw-bold text-dark">{{ $approvable->userable->getApproverLabel() }}</span>
                                                                <small class="text-muted">{{ ucfirst($approvable->type) }}</small>
                                                            </td>
                                                            <td><span class="badge bg-soft-info text-info">Lv. {{ $approvable->level }}</span></td>
                                                            <td>
                                                                <span class="badge bg-{{ $approvable->result->color() }} text-white">
                                                                    <i class="{{ $approvable->result->icon() }} me-1"></i> {{ $approvable->result->label() }}
                                                                </span>
                                                            </td>
                                                            <td class="text-wrap" style="min-width: 200px;">
                                                                {{ $approvable->reason ?: '-' }}
                                                                @if ($approvable->history)
                                                                    <div class="mt-1 small text-muted font-italic">
                                                                        <strong>Riwayat:</strong> {{ $approvable->history->reason }}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4 fw-bold text-dark">
                                    <i class="mdi mdi-account-circle-outline me-1"></i> Data Karyawan
                                </h5>
                                <div class="list-group list-group-flush">
                                    @foreach (array_filter([
                                        'Nama Karyawan' => $vacation->quota->employee->user->name,
                                        'NIP' => $vacation->quota->employee->kd ?: '-',
                                        'Jabatan' => $vacation->quota->employee->position->position->name ?? '-',
                                        'Departemen' => $vacation->quota->employee->position->position->department->name ?? '-',
                                        'Manajer' => $vacation->quota->employee->position->position->parents->firstWhere('level.value', 4)?->employees->first()->user->name,
                                    ]) as $label => $value)
                                        <div class="list-group-item px-0 py-3 border-light">
                                            <small class="text-muted d-block font-size-11 text-uppercase fw-bold">{{ $label }}</small>
                                            <span class="text-dark fw-medium">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-3">
                            @if ($vacation->can('deleted'))
                                <form class="form-block form-confirm mb-2" action="{{ route('portal::vacation.submission.destroy', ['vacation' => $vacation->id]) }}" method="post">
                                    @csrf @method('delete')
                                    <button type="submit" class="btn btn-white w-100 border text-start p-3 shadow-sm hover-shadow transition" style="border-radius: 10px;">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-soft-danger text-danger font-size-18">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-danger fw-bold font-size-14">Batalkan Pengajuan</h6>
                                                <small class="text-muted">Hapus data sebelum diproses atasan.</small>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            @endif

                            @if ($vacation->can('canceled'))
                                <a href="{{ route('portal::vacation.cancelation.show', ['vacation' => $vacation->id]) }}" class="btn btn-white w-100 border text-start p-3 shadow-sm hover-shadow transition" style="border-radius: 10px;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title rounded-circle bg-soft-warning text-warning font-size-18">
                                                <i class="mdi mdi-progress-upload"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-bold font-size-14">Ajukan Pembatalan</h6>
                                            <small class="text-muted">Untuk cuti yang sudah disetujui.</small>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

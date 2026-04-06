@extends('portal::layouts.default')

@section('title', 'Detail Kegiatan | ' . env('APP_NAME'))

@section('navtitle', 'Insentif')

@section('contents')
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
                            <a href="{{ request('next', route('portal::outwork.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Detail Insentif Kegiatan</h4>
                                <p class="text-muted mb-0 font-size-13">Informasi lengkap pengajuan kegiatan tambahan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($outwork->trashed())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian!</strong> Pengajuan ini telah dihapus. Anda tidak lagi dapat mengelola data ini.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h5 class="card-title mb-0 fw-bold text-primary">
                                        <i class="mdi mdi-information-outline me-1"></i> Rincian Pengajuan
                                    </h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Tanggal Pengajuan</label>
                                        <p class="mb-0 text-dark fw-medium">{{ $outwork->created_at->formatLocalized('%A, %d %B %Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Nama Kegiatan</label>
                                        <p class="mb-0 text-dark fw-medium">{{ $outwork->name }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-2 d-block">Jadwal Pelaksanaan</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($outwork->dates as $date)
                                                <span class="badge bg-soft-secondary text-dark px-3 py-2 fw-medium">
                                                    @isset($date['p'])
                                                        <i class="mdi mdi-clock-alert-outline text-danger me-1" title="Persiapan"></i>
                                                    @endisset
                                                    {{ strftime('%d %B %Y', strtotime($date['d'])) }}
                                                    @isset($date['t_s'])
                                                        <span class="ms-1">({{ $date['t_s'] }} @isset($date['t_e']) - {{ $date['t_e'] }} @endisset)</span>
                                                    @endisset
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Kategori</label>
                                        <p class="mb-0 text-dark">
                                            {{ $outwork->category?->name ?: '-' }}
                                            @isset($outwork->category->description)
                                                <small class="text-muted d-block">{{ $outwork->category->description }}</small>
                                            @endisset
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Status Saat Ini</label>
                                        <div>@include('portal::outwork.components.status', ['outwork' => $outwork])</div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Deskripsi / Catatan</label>
                                        <p class="mb-0 text-dark">{{ $outwork->description ?: '-' }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-1 d-block">Lampiran Dokumentasi</label>
                                        @if (isset($outwork->attachment) && Storage::exists($outwork->attachment))
                                            <a href="{{ Storage::url($outwork->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info waves-effect waves-light mt-1">
                                                <i class="mdi mdi-file-document-outline me-1"></i> Lihat Lampiran
                                            </a>
                                        @else
                                            <p class="mb-0 text-muted italic">Tidak ada lampiran yang diunggah</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Timeline Persetujuan --}}
                                @if ($outwork->approvables->count())
                                    <div class="mt-5">
                                        <h6 class="text-muted font-size-12 text-uppercase fw-bold mb-3">Alur Verifikasi</h6>
                                        <div class="table-responsive">
                                            <table class="table table-nowrap align-middle table-borderless bg-light rounded-3 overflow-hidden">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3 text-muted">Penanggungjawab</th>
                                                        <th class="text-muted">Keputusan</th>
                                                        <th class="text-muted">Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($outwork->approvables as $approvable)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <span class="d-block fw-bold text-dark">{{ $approvable->userable->getApproverLabel() }}</span>
                                                                <small class="text-muted text-uppercase">{{ ucfirst($approvable->type) }} Lv.{{ $approvable->level }}</small>
                                                            </td>
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
                        @include('portal::components.employee-detail-card', ['employee' => $outwork->employee])

                        {{-- Action Buttons --}}
                        @unless ($outwork->hasAnyApprovableResultIn('REJECT') || !$outwork->hasApprovables() || $outwork->trashed())
                            @if ($outwork->hasAllApprovableResultIn('PENDING') || $outwork->hasAnyApprovableResultIn('REVISION') || !$outwork->hasApprovables())
                                <div class="mt-3">
                                    <form class="form-block form-confirm" action="{{ route('portal::outwork.submission.destroy', ['outwork' => $outwork->id]) }}" method="post">
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
                                                    <small class="text-muted">Hapus data sebelum diproses lebih lanjut.</small>
                                                </div>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endunless
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

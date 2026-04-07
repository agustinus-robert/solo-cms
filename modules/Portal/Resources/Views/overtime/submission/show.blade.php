@extends('portal::layouts.index')

@section('title', 'Detail Lembur | ' . env('APP_NAME'))

@section('navtitle', 'Lembur')

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

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Header Halaman --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::overtime.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Detail Pengajuan Lembur</h4>
                                <p class="text-muted mb-0 font-size-13">Informasi lengkap rincian dan status lembur.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($overtime->trashed())
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        <strong>Perhatian!</strong> Pengajuan ini telah dihapus.
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4 text-primary d-flex align-items-center">
                                    <i class="mdi mdi-information-outline me-2 font-size-20"></i> Data Pengajuan
                                </h5>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <p class="text-muted font-size-12 mb-1">Nama Kegiatan / Pekerjaan</p>
                                        <h6 class="fw-bold text-dark">{{ $overtime->name }}</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted font-size-12 mb-1">Tanggal Pengajuan</p>
                                        <h6 class="fw-bold">{{ $overtime->created_at->translatedFormat('l, d F Y') }}</h6>
                                    </div>
                                </div>

                                {{-- Jadwal & Realisasi --}}
                                <div class="mb-4">
                                    <p class="text-muted font-size-12 mb-2">Jadwal yang Diajukan</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if ($overtime->schedules)
                                            @foreach ($overtime->schedules as $date)
                                                <span class="badge badge-soft-secondary font-size-12 p-2 border border-secondary border-opacity-10">
                                                    <i class="mdi mdi-calendar-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($date['d'])->translatedFormat('d F Y') }}
                                                    <span class="text-muted mx-1">|</span>
                                                    {{ $date['t_s'] }} - {{ $date['t_e'] ?? '??' }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                @if($overtime->dates)
                                <div class="mb-4">
                                    <p class="text-muted font-size-12 mb-2">Realisasi Pelaksanaan</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($overtime->dates as $date)
                                            <span class="badge badge-soft-success font-size-12 p-2 border border-success border-opacity-10">
                                                <i class="mdi mdi-check-decagram me-1"></i>
                                                {{ \Carbon\Carbon::parse($date['d'])->translatedFormat('d F Y') }}
                                                <span class="text-muted mx-1">|</span>
                                                {{ $date['t_s'] }} - {{ $date['t_e'] ?? '??' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="mb-4">
                                    <p class="text-muted font-size-12 mb-1">Deskripsi / Catatan</p>
                                    <div class="p-3 bg-light rounded text-dark font-size-13 border border-dashed">
                                        {{ $overtime->description ?: 'Tidak ada deskripsi tambahan.' }}
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-sm-6">
                                        <p class="text-muted font-size-12 mb-1">Status Saat Ini</p>
                                        @include('portal::overtime.components.status', ['overtime' => $overtime])
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted font-size-12 mb-1">Lampiran</p>
                                        @if ($overtime->attachment && Storage::exists($overtime->attachment))
                                            <a href="{{ Storage::url($overtime->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info py-1">
                                                <i class="mdi mdi-file-document-outline me-1"></i> Lihat Berkas
                                            </a>
                                        @else
                                            <span class="text-muted font-size-13 italic">Tidak ada lampiran</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Info Superior --}}
                            @if(count($superiors) > 0)
                                <div class="card-footer bg-light bg-opacity-50 border-top p-4">
                                    <h6 class="text-muted font-size-12 text-uppercase fw-bold mb-3">Struktur Atasan Penanggung Jawab</h6>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($superiors as $sup)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-14">
                                                        {{ $sup['step'] }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-size-13 fw-bold">{{ $sup['label'] }}</h6>
                                                    <small class="text-muted">Wajib disetujui oleh level ini.</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Riwayat Persetujuan --}}
                            @if ($overtime->approvables->count())
                                <div class="card-footer bg-transparent border-top p-4">
                                    <h6 class="text-muted font-size-12 text-uppercase fw-bold mb-4">Log Persetujuan Atasan</h6>
                                    <div class="row g-4">
                                        @foreach ($overtime->approvables as $approvable)
                                            <div class="col-md-6 border-start border-3 border-{{ $approvable->result->color() }} ps-3">
                                                <div class="d-flex justify-content-between mb-1 align-items-center">
                                                    <span class="text-muted font-size-11">Level {{ $approvable->level }}</span>
                                                    <span class="badge badge-soft-{{ $approvable->result->color() }} font-size-10 px-2">{{ $approvable->result->label() }}</span>
                                                </div>
                                                <h6 class="mb-1 font-size-14 fw-bold">{{ $approvable->userable->getApproverLabel() }}</h6>
                                                @if($approvable->reason)
                                                    <p class="text-muted font-size-12 italic mb-0 bg-light p-1 rounded">"{{ $approvable->reason }}"</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xl-4">
                        @include('portal::components.employee-detail-card', ['employee' => $overtime->employee])

                       <div class="mt-4">
                            @php
                                $myApproval = $overtime->approvables->where('userable_id', $employee->position->id)->first();
                                $isApprover = !is_null($myApproval);
                                $isOwner = $overtime->empl_id == $employee->id;
                                $isStatusPending = ($myApproval && $myApproval->result->value == 0);
                            @endphp

                            @if($isApprover && $isStatusPending && !$overtime->trashed())
                                <form class="form-block form-confirm mb-3" action="{{ route('portal::overtime.submission.approve', $overtime->id) }}" method="post">
                                    @csrf
                                    @method('put')
                                    <button class="btn btn-soft-success w-100 py-3 text-start border-success border-opacity-10 shadow-sm">
                                        <i class="mdi mdi-check-circle-outline mdi-24px float-end text-success opacity-25"></i>
                                        <h6 class="text-success mb-1 font-size-15 fw-bold">Terima Pengajuan</h6>
                                        <p class="text-muted font-size-11 mb-0">Setujui lembur ini sekarang.</p>
                                    </button>
                                </form>

                                {{-- FORM TOLAK (Langsung Tanpa Modal) --}}
                                <div class="p-3 border border-warning border-opacity-20 rounded bg-light bg-opacity-50 mb-3">
                                    <form action="{{ route('portal::overtime.submission.destroy', $overtime->id) }}" method="POST">
                                        @csrf
                                        <h6 class="text-warning mb-2 font-size-13 fw-bold">Tolak / Revisi:</h6>
                                        <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Konfirmasi Tolak
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if($isOwner || $isApprover)
                                @unless ($overtime->hasAnyApprovableResultIn('REJECT') || !$overtime->hasApprovables() || $overtime->trashed())
                                    @if ($overtime->hasAllApprovableResultIn(0) || $overtime->hasAnyApprovableResultIn('REVISION'))
                                        <form class="form-block form-confirm" action="{{ route('portal::overtime.submission.destroy', ['overtime' => $overtime->id]) }}" method="post">
                                            @csrf @method('delete')
                                            <button class="btn btn-soft-danger w-100 py-3 text-start border-danger border-opacity-10 shadow-sm">
                                                <i class="mdi mdi-delete-outline mdi-24px float-end text-danger opacity-25"></i>
                                                <h6 class="text-danger mb-1 font-size-15 fw-bold">Batalkan Pengajuan</h6>
                                                <p class="text-muted font-size-11 mb-0">Hapus data pengajuan ini dari sistem.</p>
                                            </button>
                                        </form>
                                    @endif
                                @endunless
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@extends('portal::layouts.index')

@section('title', 'Detail Izin | ' . env('APP_NAME'))

@section('contents')
    <style>
        .card-detail { border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); mb-4; }
        .label-muted { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #74788d; margin-bottom: 4px; }
        .value-bold { font-weight: 600; color: #495057; font-size: 0.9rem; }
        .info-section { padding: 1.25rem; border-bottom: 1px solid #eff2f7; }
        .info-section:last-child { border-bottom: none; }
        .avatar-title-custom { background-color: rgba(85, 110, 230, 0.1); color: #556ee6; font-weight: bold; }
    </style>

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
            <div class="container-fluid">

                {{-- Header & Aksi --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::leave.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Detail Pengajuan Izin</h4>
                                <p class="text-muted mb-0 font-size-13">ID Pengajuan: #LEAVE-{{ $leave->id }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        @if (!$leave->trashed())
                            <a href="{{ route('portal::leave.print', ['leave' => $leave->id]) }}" target="_blank" class="btn btn-light waves-effect me-2">
                                <i class="mdi mdi-printer-outline me-1"></i> Cetak Dokumen
                            </a>
                        @endif
                    </div>
                </div>

                @if ($leave->trashed())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        <strong>Pengajuan Dibatalkan:</strong> Data ini telah dihapus dan tidak dapat dikelola kembali.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Sisi Kiri: Informasi Izin --}}
                    <div class="col-xl-8">
                        <div class="card card-detail">
                            <div class="card-header bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-information-outline text-primary font-size-20 me-2"></i>
                                    <h5 class="card-title mb-0">Informasi Utama</h5>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="info-section">
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <p class="label-muted">Kategori Izin</p>
                                            <span class="badge badge-soft-primary font-size-12 px-2 py-1">{{ $leave->category->name }}</span>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p class="label-muted">Tanggal Pengajuan</p>
                                            <p class="value-bold mb-0">{{ $leave->created_at->translatedFormat('l, d F Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-section bg-light-soft">
                                    <p class="label-muted mb-2">Tanggal Izin Yang Diajukan</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($leave->dates as $date)
                                            <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center" style="min-width: 180px;">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-info text-info">
                                                        <i class="mdi mdi-calendar-check"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 font-size-13 fw-bold">{{ date('d M Y', strtotime($date['d'])) }}</p>
                                                    <p class="mb-0 font-size-11 text-muted">
                                                        @isset($date['t_s']) {{ $date['t_s'] }} @else Full Day @endisset
                                                        @isset($date['t_e']) - {{ $date['t_e'] }} @endisset
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="info-section">
                                    <p class="label-muted">Deskripsi / Alasan</p>
                                    <p class="text-dark bg-light p-3 rounded border-start border-primary border-3">{{ $leave->description ?: 'Tidak ada deskripsi tambahan.' }}</p>
                                </div>

                                <div class="info-section">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <p class="label-muted">Status Saat Ini</p>
                                            @include('portal::leave.components.status', ['leave' => $leave])
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p class="label-muted">Lampiran</p>
                                            @if (isset($leave->attachment) && Storage::exists($leave->attachment))
                                                <a href="{{ Storage::url($leave->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info">
                                                    <i class="mdi mdi-file-document-outline me-1"></i> Lihat Dokumen Pendukung
                                                </a>
                                            @else
                                                <span class="text-muted italic font-size-12">Tidak ada lampiran</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Persetujuan Berjenjang --}}
                        @if ($leave->approvables->count())
                            <div class="card card-detail">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">Alur Persetujuan</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($leave->approvables as $approvable)
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <span class="avatar-title rounded-circle bg-{{ $approvable->result->color() }} text-white">
                                                        <i class="{{ $approvable->result->icon() }}"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">{{ $approvable->userable->getApproverLabel() }}</h6>
                                                        <p class="text-muted font-size-12 mb-0">{{ ucfirst($approvable->type) }} Level {{ $approvable->level }}</p>
                                                    </div>
                                                    <span class="badge badge-soft-{{ $approvable->result->color() }} font-size-11">{{ $approvable->result->label() }}</span>
                                                </div>
                                                @if($approvable->reason)
                                                    <div class="mt-2 p-2 bg-light rounded font-size-13 italic text-secondary">
                                                        "{{ $approvable->reason }}"
                                                    </div>
                                                @endif

                                                @if ($approvable->history)
                                                    <div class="mt-2 ps-3 border-start">
                                                        <p class="text-muted font-size-11 mb-0">Catatan Sebelumnya:</p>
                                                        <p class="font-size-12 text-secondary mb-0">{{ $approvable->history->reason }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if (!$loop->last) <hr class="my-3 border-light"> @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Sisi Kanan: Profil Karyawan & Aksi Hapus --}}
                    <div class="col-xl-4">
                        <div class="card card-detail overflow-hidden">
                            <div class="bg-primary bg-soft p-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-md">
                                            <span class="avatar-title rounded-circle avatar-title-custom font-size-24">
                                                {{ strtoupper(substr($leave->employee->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 align-self-center">
                                        <div class="text-primary">
                                            <h5 class="text-primary mb-1">{{ $leave->employee->user->name }}</h5>
                                            <p class="text-primary opacity-75 mb-0 font-size-13">{{ $leave->employee->kd ?: 'NIP Kosong' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row mt-3">
                                    <div class="col-12 mb-3">
                                        <p class="label-muted">Jabatan</p>
                                        <p class="value-bold">{{ $leave->employee->position->position->name ?? '-' }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <p class="label-muted">Departemen</p>
                                        <p class="value-bold">{{ $leave->employee->position->position->department->name ?? '-' }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="label-muted">Atasan Langsung / Manajer</p>
                                        <p class="value-bold mb-0">
                                            {{ $leave->employee->position->position->parents->firstWhere('level.value', 4)?->employees->first()->user->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Batalkan --}}
                        @unless(!$leave->hasApprovables() || $leave->trashed())
                            @if ($leave->hasAllApprovableResultIn('PENDING') || $leave->hasAnyApprovableResultIn('REVISION') || $leave->hasAnyApprovableResultIn('REJECT'))
                                <div class="card card-detail border-danger-subtle bg-danger-subtle bg-opacity-10 mt-4">
                                    <div class="card-body p-3">
                                        <form class="form-block form-confirm" action="{{ route('portal::leave.submission.destroy', ['leave' => $leave->id]) }}" method="post">
                                            @csrf @method('delete')
                                            <h6 class="text-danger mb-2">Batalkan Pengajuan?</h6>
                                            <p class="text-muted font-size-12 mb-3">Anda masih dapat membatalkan pengajuan ini selama belum disetujui sepenuhnya oleh atasan.</p>
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="mdi mdi-trash-can-outline me-1"></i> Batalkan & Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endunless
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

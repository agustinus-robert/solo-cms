@extends('portal::layouts.index')

@section('title', 'Detail Pengajuan Lembur | ' . env('APP_NAME'))

@section('contents')
    <style>
        .card-detail { border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .label-muted { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #74788d; margin-bottom: 4px; }
        .value-bold { font-weight: 600; color: #495057; font-size: 0.9rem; }
        .info-section { padding: 1.25rem; border-bottom: 1px solid #eff2f7; }
        .info-section:last-child { border-bottom: none; }
        .avatar-title-custom { background-color: rgba(85, 110, 230, 0.1); color: #556ee6; font-weight: bold; }
        .bg-light-soft { background-color: #f8f9fa; }
    </style>

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
                            <a href="{{ request('next', route('portal::overtime.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold">Detail Pengajuan Lembur</h4>
                                <p class="text-muted mb-0 font-size-13">ID Pengajuan: #OT-{{ $overtime->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($overtime->trashed())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        <strong>Pengajuan Dibatalkan:</strong> Data ini telah dihapus dan tidak dapat dikelola kembali.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        {{-- Kartu Informasi Utama --}}
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
                                            <p class="label-muted">Nama Kegiatan / Pekerjaan</p>
                                            <p class="value-bold mb-0 text-primary">{{ $overtime->name }}</p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p class="label-muted">Tanggal Pengajuan</p>
                                            <p class="value-bold mb-0">{{ $overtime->created_at->translatedFormat('l, d F Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-section bg-light-soft">
                                    <p class="label-muted mb-2">Realisasi Waktu Lembur</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($overtime->dates as $date)
                                            <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center" style="min-width: 190px;">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-success text-success">
                                                        <i class="mdi mdi-clock-check-outline"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 font-size-13 fw-bold">{{ date('d M Y', strtotime($date['d'])) }}</p>
                                                    <p class="mb-0 font-size-11 text-muted">
                                                        {{ $date['t_s'] }} - {{ $date['t_e'] ?? '??' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="info-section">
                                    <p class="label-muted">Deskripsi / Alasan Lembur</p>
                                    <p class="text-dark bg-light p-3 rounded border-start border-primary border-3">
                                        {{ $overtime->description ?: 'Tidak ada deskripsi tambahan.' }}
                                    </p>
                                </div>

                                <div class="info-section">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <p class="label-muted">Status Saat Ini</p>
                                            @include('portal::overtime.components.status', ['overtime' => $overtime])
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p class="label-muted">Lampiran</p>
                                            @if ($overtime->attachment)
                                                <a href="{{ Storage::url($overtime->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info">
                                                    <i class="mdi mdi-file-document-outline me-1"></i> Lihat Berkas
                                                </a>
                                            @else
                                                <span class="text-muted italic font-size-12">Tidak ada lampiran</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Log Persetujuan --}}
                        @if ($overtime->approvables->count())
                            <div class="card card-detail">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">Alur Persetujuan</h5>
                                </div>
                                <div class="card-body">
                                    @php $myPositionIds = $employee->positions()->pluck('id')->toArray(); @endphp
                                    @foreach ($overtime->approvables as $approvable)
                                        @php $isMyTurn = in_array($approvable->userable_id, $myPositionIds) && $approvable->result->value == 0; @endphp
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <span class="avatar-title rounded-circle bg-{{ $approvable->result->color() }} text-white">
                                                        <i class="mdi mdi-account-check"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">{{ $approvable->userable->getApproverLabel() }}</h6>
                                                        <p class="text-muted font-size-12 mb-0">Approval Level {{ $approvable->level }}</p>
                                                    </div>
                                                    <span class="badge badge-soft-{{ $approvable->result->color() }} font-size-11">{{ $approvable->result->label() }}</span>
                                                </div>

                                                {{-- Area Input Form Jika Giliran User --}}
                                                @if ($isMyTurn && !$overtime->trashed())
                                                    <form action="{{ route('portal::overtime.manage.update', $approvable->id) }}" method="post" class="mt-3 bg-light p-2 rounded border">
                                                        @csrf @method('put')
                                                        <div class="row g-2">
                                                            <div class="col-sm-8">
                                                                <input type="text" name="reason" class="form-control form-control-sm" placeholder="Tulis catatan (opsional)...">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="btn-group btn-group-sm w-100">
                                                                    <button type="submit" name="result" value="1" class="btn btn-success w-50">Setuju</button>
                                                                    <button type="submit" name="result" value="2" class="btn btn-danger w-50">Tolak</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @elseif($approvable->reason)
                                                    <div class="mt-2 p-2 bg-light rounded font-size-13 italic text-secondary">
                                                        "{{ $approvable->reason }}"
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

                    <div class="col-xl-4">
                        {{-- Karyawan Detail Card --}}
                        <div class="card card-detail overflow-hidden">
                            <div class="bg-primary bg-soft p-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-md">
                                            <span class="avatar-title rounded-circle avatar-title-custom font-size-24">
                                                {{ strtoupper(substr($overtime->employee->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 align-self-center">
                                        <div class="text-primary">
                                            <h5 class="text-primary mb-1">{{ $overtime->employee->user->name }}</h5>
                                            <p class="text-primary opacity-75 mb-0 font-size-13">{{ $overtime->employee->kd ?: 'NIP Kosong' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row mt-3">
                                    <div class="col-12 mb-3">
                                        <p class="label-muted">Jabatan</p>
                                        <p class="value-bold">{{ $overtime->employee->position->position->name ?? '-' }}</p>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <p class="label-muted">Departemen</p>
                                        <p class="value-bold">{{ $overtime->employee->position->position->department->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Struktur Atasan Berdasarkan Posisi --}}
                        @if(count($superiors) > 0)
                            <div class="card card-detail">
                                <div class="card-header bg-transparent border-bottom">
                                    <h6 class="mb-0 fw-bold font-size-13 text-uppercase">Hierarchy Approval</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($superiors as $sup)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                        {{ $sup['step'] }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-size-12 fw-bold text-dark">{{ $sup['label'] }}</h6>
                                                    <small class="text-muted">Approval Lv. {{ $sup['step'] }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Tombol Batalkan --}}
                        @if($overtime->empl_id == $employee->id && !$overtime->trashed())
                            @if ($overtime->approvables->where('result', '!=', 0)->isEmpty())
                                <div class="card card-detail border-danger-subtle bg-danger-subtle bg-opacity-10 mt-4">
                                    <div class="card-body p-3">
                                        <form class="form-confirm" action="{{ route('portal::overtime.submission.destroy', $overtime->id) }}" method="post">
                                            @csrf @method('delete')
                                            <h6 class="text-danger mb-2">Batalkan Pengajuan?</h6>
                                            <p class="text-muted font-size-12 mb-3">Batalkan selama belum disetujui atasan.</p>
                                            <button type="submit" class="btn btn-danger w-100 btn-sm">
                                                <i class="mdi mdi-trash-can-outline me-1"></i> Batalkan & Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

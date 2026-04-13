@extends('portal::layouts.index')

@section('title', 'Detail Pengajuan Cuti | ' . env('APP_NAME'))

@section('navtitle', 'Cuti')

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

    {{-- Header Topbar --}}
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
            </div>
            <div class="d-flex">
                @include('portal::layouts.components.notifications')
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

                {{-- Header Section --}}
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ request('next', route('portal::vacation.submission.index')) }}" class="btn btn-sm btn-soft-secondary rounded-circle me-3">
                                <i class="mdi mdi-arrow-left font-size-18"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Detail Pengajuan Cuti</h4>
                                <p class="text-muted mb-0 font-size-13">ID Pengajuan: #VAC-{{ $vacation->id }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        @if (!$vacation->trashed())
                            <a class="btn btn-light waves-effect me-2" href="{{ route('portal::vacation.print', ['vacation' => $vacation->id]) }}" target="_blank">
                                <i class="mdi mdi-printer-outline me-1"></i> Cetak Dokumen
                            </a>
                        @endif
                    </div>
                </div>

                @if ($vacation->trashed())
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <i class="mdi mdi-block-helper me-2"></i>
                        <strong>Pengajuan Dibatalkan:</strong> Data ini telah dihapus.
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
                                            <p class="label-muted">Kategori Cuti</p>
                                            <span class="badge badge-soft-primary font-size-12 px-2 py-1">{{ $vacation->quota->category->name }}</span>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p class="label-muted">Tanggal Pengajuan</p>
                                            <p class="value-bold mb-0">{{ $vacation->created_at->translatedFormat('l, d F Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-section bg-light-soft">
                                    <p class="label-muted mb-2">Tanggal Cuti Yang Diajukan</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @isset(collect($vacation->dates)->first()['cashable'])
                                            <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center w-100 text-center">
                                                <h6 class="mb-0 text-dark fw-bold w-100">{{ $vacation->dates->count() }} Hari dikompensasikan (Cashable)</h6>
                                            </div>
                                        @else
                                            @foreach ($vacation->dates as $date)
                                                <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center" style="min-width: 180px;">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-info text-info">
                                                            <i class="mdi mdi-calendar-check"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 font-size-13 fw-bold {{ isset($date['c']) ? 'text-decoration-line-through text-muted' : '' }}">
                                                            {{ \Carbon\Carbon::parse($date['d'])->translatedFormat('d M Y') }}
                                                        </p>
                                                        <small class="text-muted">@isset($date['f']) Freelance Mode @else Full Day @endisset</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endisset
                                    </div>
                                </div>

                                <div class="info-section">
                                    <p class="label-muted">Alasan / Deskripsi</p>
                                    <p class="text-dark bg-light p-3 rounded border-start border-primary border-3">{{ $vacation->description ?: '-' }}</p>
                                </div>

                                <div class="info-section">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <p class="label-muted">Status Saat Ini</p>
                                            @include('portal::vacation.components.status', ['vacation' => $vacation])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $user = auth()->user();
                            $myPositionIds = $user->employee->positions()->pluck('id')->toArray();

                            $approvalArray = $vacation->approvables->sortBy('level');
                            $currentActiveId = null;
                            $pendingApprovals = $approvalArray->where('result', 0)->sortByDesc('level');

                            if($pendingApprovals->count() > 0) {
                                $currentActiveId = $pendingApprovals->first()->id;
                            }
                        @endphp

                        @if ($approvalArray->count() > 0)
                            <div class="card card-detail">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">Alur Persetujuan</h5>
                                </div>
                                <div class="card-body">
                                    @foreach ($approvalArray as $approvable)
                                        @php
                                            $statusValue = is_object($approvable->result) ? $approvable->result->value : $approvable->result;
                                            $isMyTurn = ($approvable->id === $currentActiveId) && in_array($approvable->userable_id, $myPositionIds);
                                        @endphp

                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0 me-3 text-center">
                                                <div class="avatar-sm mx-auto">
                                                    <span class="avatar-title rounded-circle bg-{{ $statusValue == 0 ? ($approvable->id === $currentActiveId ? 'warning' : 'light') : $approvable->result->color() }} {{ $statusValue == 0 && $approvable->id !== $currentActiveId ? 'text-muted' : 'text-white' }} shadow-sm">
                                                        <i class="mdi {{ $statusValue == 0 ? 'mdi-clock-outline' : 'mdi-check-bold' }}"></i>
                                                    </span>
                                                </div>
                                                @if (!$loop->last)
                                                    <div class="border-start border-2 border-light mx-auto mt-2" style="height: 20px; width: 1px;"></div>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold {{ $statusValue == 0 && $approvable->id !== $currentActiveId ? 'text-muted' : 'text-dark' }}">
                                                            {{ $approvable->userable->getApproverLabel() }}
                                                        </h6>
                                                        <p class="text-muted font-size-12 mb-0">Level <strong>{{ $approvable->level }}</strong></p>
                                                    </div>
                                                    <span class="badge badge-soft-{{ $approvable->result->color() }} font-size-11 px-2 py-1">
                                                        {{ $approvable->result->label() }}
                                                    </span>
                                                </div>

                                                @if ($isMyTurn && !$vacation->trashed())
                                                    <div class="mt-3 bg-light p-3 rounded border border-warning border-opacity-20 shadow-sm">
                                                        <form action="{{ route('portal::vacation.manage.update', $approvable->id) }}" method="post">
                                                            @csrf @method('put')
                                                            <input type="hidden" name="approvable_id" value="{{ $approvable->id }}">
                                                            <input type="hidden" name="next" value="{{ url()->current() }}">

                                                            <div class="row g-2">
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Tulis catatan (Jika menolak pengajuan libur!)..." autofocus>
                                                                </div>
                                                                <div class="col-sm-4 text-end">
                                                                    <div class="btn-group btn-group-sm">
                                                                        <button type="submit" name="result" value="1" class="btn btn-success px-3">Setuju</button>
                                                                        <button type="submit" name="result" value="2" class="btn btn-danger px-3">Tolak</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @elseif($approvable->reason)
                                                    <div class="mt-2 p-2 bg-light rounded font-size-13 italic text-secondary border-start border-3 border-primary">
                                                        "{{ $approvable->reason }}"
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-xl-4">
                        {{-- Data Karyawan --}}
                        @php
                            $emp = $vacation->quota->employee;
                            $pos = $emp->position->position ?? null;
                        @endphp
                        <div class="card card-detail overflow-hidden">
                            <div class="bg-primary bg-soft p-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-md">
                                            <span class="avatar-title rounded-circle avatar-title-custom font-size-24">
                                                {{ strtoupper(substr($emp->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 align-self-center">
                                        <div class="text-primary">
                                            <h5 class="text-primary mb-1">{{ $emp->user->name }}</h5>
                                            <p class="text-primary opacity-75 mb-0 font-size-13">{{ $emp->kd ?: 'NIP -' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row mt-3">
                                    <div class="col-12 mb-3">
                                        <p class="label-muted">Jabatan</p>
                                        <p class="value-bold">{{ $pos->name ?? '-' }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="label-muted">Departemen</p>
                                        <p class="value-bold mb-0">{{ $pos->department->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Batalkan --}}
                        @php $hasDecisionByAnyone = $vacation->approvables->where('result', '!=', 0)->count() > 0; @endphp
                        @if ($vacation->can('deleted') && !$hasDecisionByAnyone && !$vacation->trashed())
                            <div class="card card-detail border-danger-subtle bg-danger-subtle bg-opacity-10 mt-4">
                                <div class="card-body p-3">
                                    <form class="form-confirm" action="{{ route('portal::vacation.submission.destroy', ['vacation' => $vacation->id]) }}" method="post">
                                        @csrf @method('delete')
                                        <h6 class="text-danger mb-2 font-size-14 fw-bold">Batalkan Pengajuan?</h6>
                                        <p class="text-muted font-size-12 mb-3">Hapus data ini sebelum diproses oleh atasan.</p>
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="mdi mdi-trash-can-outline me-1"></i> Batalkan & Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

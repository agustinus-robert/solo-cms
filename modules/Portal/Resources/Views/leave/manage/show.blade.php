@extends('portal::layouts.index')

@section('title', 'Kelola Pengajuan | ')

{{-- Topbar Title --}}
@section('navtitle', 'Kelola Perizinan')

@section('contents')
    {{-- TOPBAR START --}}
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('skote/images/logo.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('skote/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="" class="logo logo-light">
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
                <div class="dropdown d-none d-lg-inline-block ms-1">
                    <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-customize"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <div class="px-lg-2">
                            @if (env('DEMO') == 0)
                                <div class="row no-gutters">
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="{{ route('hrms::dashboard') }}">
                                            <i class="bx bxs-user-pin" style='font-size:30px;'></i>
                                            <span>HRMS</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="{{ route('portal::dashboard-msdm.index') }}">
                                            <i class="bx bxs-briefcase-alt-2" style='font-size:30px;'></i>
                                            <span>MSDM</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="{{ route('finance::dashboard') }}">
                                            <i class="bx bx-money" style='font-size:30px;'></i>
                                            <span>Finance</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @include('layouts.nav_name')
            </div>
        </div>
    </header>
    {{-- TOPBAR END --}}

    {{-- MAIN CONTENT START --}}
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header Section --}}
            <div class="d-flex align-items-center mb-4">
                <a class="text-decoration-none text-dark" href="{{ request('next', route('portal::leave.manage.index')) }}">
                    <i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i>
                </a>
                <div class="ms-4">
                    <h2 class="mb-1 fw-bold">Kelola Pengajuan</h2>
                    <div class="text-muted">Kelola persetujuan izin staf di bawah koordinasi Anda secara dinamis.</div>
                </div>
            </div>

            @if ($leave->trashed())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <i class="mdi mdi-alert-octagon-outline me-2"></i>
                    <strong>Perhatian!</strong> Pengajuan ini telah dihapus. Anda tidak dapat lagi mengelola data ini.
                </div>
            @endif

            <div class="row">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="fw-bold"><i class="mdi mdi-eye-outline me-1"></i> Detail Pengajuan</div>
                            @if (!$leave->trashed())
                                <a class="btn btn-soft-success btn-sm rounded px-3" href="{{ route('portal::leave.print', ['leave' => $leave->id]) }}" target="_blank">
                                    <i class="mdi mdi-printer-outline me-1"></i> <span class="d-none d-sm-inline">Cetak PDF</span>
                                </a>
                            @endif
                        </div>

                        <div class="card-body border-top">
                            <div class="row gy-4 mb-4">
                                <div class="col-md-6">
                                    <div class="small text-muted text-uppercase font-size-11 fw-bold">Tanggal Pengajuan</div>
                                    <div class="fw-bold text-dark"> {{ $leave->created_at->isoFormat('dddd, D MMMM YYYY') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted text-uppercase font-size-11 fw-bold">Kategori Izin</div>
                                    <div class="fw-bold">
                                        <span class="badge bg-soft-primary text-primary px-2">{{ $leave->category->name }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="small text-muted text-uppercase font-size-11 fw-bold mb-2">Tanggal Izin yang Diajukan</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($leave->dates as $date)
                                        <span class="badge bg-light text-dark border fw-normal p-2" @isset($date['f']) data-bs-toggle="tooltip" title="Sebagai freelancer" @endisset style="font-size: 13px;">
                                            @isset($date['f']) <i class="mdi mdi-account-network-outline text-danger me-1"></i> @endif
                                            {{ \Carbon\Carbon::parse($date['d'])->isoFormat('D MMMM YYYY') }}
                                            @isset($date['t_s']) <small class="text-muted ms-1">({{ $date['t_s'] }} - {{ $date['t_e'] ?? 'Selesai' }})</small> @endisset
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="small text-muted text-uppercase font-size-11 fw-bold mb-1">Deskripsi / Alasan</div>
                                <div class="p-3 bg-light rounded border-start border-primary border-3">
                                    {{ $leave->description ?: 'Tidak ada alasan yang dicantumkan.' }}
                                </div>
                            </div>

                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="small text-muted text-uppercase font-size-11 fw-bold mb-1">Status Saat Ini</div>
                                    <div>@include('portal::leave.components.status', ['leave' => $leave])</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted text-uppercase font-size-11 fw-bold mb-1">Lampiran</div>
                                    @if (isset($leave->attachment) && Storage::exists($leave->attachment))
                                        <a href="{{ Storage::url($leave->attachment) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="mdi mdi-file-link-outline"></i> Lihat Lampiran
                                        </a>
                                    @else
                                        <div class="text-muted small italic">Tidak ada lampiran</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Approval Timeline --}}
                        <div class="card-header bg-light border-top border-bottom-0 py-3">
                            <h6 class="mb-0 fw-bold"><i class="mdi mdi-shield-account-outline me-1"></i> Alur Persetujuan</h6>
                        </div>

                        <div class="card-body">
                            @foreach ($leave->approvables->sortBy('level') as $approvable)
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-5">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-soft-secondary text-secondary font-size-11">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-muted small">{{ ucfirst($approvable->type) }} Level {{ $approvable->level }}</div>
                                                <strong class="text-dark">{{ $approvable->userable->getApproverLabel() }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7 mt-2 mt-md-0">
                                        @php
                                            // Hanya pemilik posisi yang bersangkutan yang bisa simpan
                                            $isTargetApprover = ($approvable->userable->position_id === $employee->position->position_id);
                                            $canEdit = $isTargetApprover && !$leave->trashed();
                                        @endphp

                                        @if ($canEdit)
                                            <form action="{{ route('portal::leave.manage.update', ['approvable' => $approvable->id, 'next' => request('next', route('portal::leave.manage.index'))]) }}" method="post">
                                                @csrf @method('PUT')
                                                <div class="input-group input-group-sm mb-2">
                                                    <select class="form-select border-primary" name="result">
                                                        @foreach ($results as $result)
                                                            <option value="{{ $result->value }}" @selected($result->value == old('result', $approvable->result->value))>
                                                                {{ $result->label() }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-primary"><i class="mdi mdi-check-bold"></i> Simpan</button>
                                                </div>
                                                <textarea class="form-control form-control-sm" name="reason" rows="2" placeholder="Berikan catatan (jika ingin ditolak!)">{{ old('reason', $approvable->reason) }}</textarea>
                                            </form>
                                        @else
                                            <div class="bg-light p-2 rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge bg-soft-{{ $approvable->result->color() }} text-{{ $approvable->result->color() }} px-2 py-1">
                                                        <i class="{{ $approvable->result->icon() }} me-1"></i> {{ $approvable->result->label() }}
                                                    </span>
                                                    @if(!$isTargetApprover)
                                                        <small class="text-muted italic" style="font-size: 10px;"><i class="mdi mdi-lock-outline"></i> Read Only</small>
                                                    @endif
                                                </div>
                                                @if($approvable->reason)
                                                    <div class="small mt-1 text-dark fw-normal border-top pt-1 opacity-75">
                                                        "{{ $approvable->reason }}"
                                                    </div>
                                                @else
                                                    <div class="small mt-1 text-muted italic pt-1 border-top" style="font-size: 11px;">Tidak ada catatan.</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if (!$loop->last) <hr class="border-light"> @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body bg-light rounded-top">
                            <h6 class="mb-0 fw-bold"><i class="mdi mdi-account-circle-outline me-1"></i> Profil Pemohon</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            @php
                                // Mengambil atasan langsung secara dinamis dari relasi parents
                                $immediateSuperior = $leave->employee->position->position->parents->first();

                                $details = [
                                    'Nama Karyawan' => $leave->employee->user->name,
                                    'NIP / ID'      => $leave->employee->kd ?: '-',
                                    'Jabatan'       => $leave->employee->position->position->name ?? '-',
                                    'Unit / Dept'   => $leave->employee->position->position->department->name ?? '-',
                                    'Atasan Langsung' => $immediateSuperior ? ($immediateSuperior->employeePositions->first()?->employee->user->name ?? 'Belum Diatur') : 'Top Level',
                                ];
                            @endphp

                            @foreach ($details as $label => $value)
                                <div class="list-group-item border-light py-3">
                                    <div class="small text-muted text-uppercase font-size-10 fw-bold mb-1">{{ $label }}</div>
                                    <div class="text-dark fw-bold">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div> {{-- end container-fluid --}}
    </div> {{-- end page-content --}}
@endsection

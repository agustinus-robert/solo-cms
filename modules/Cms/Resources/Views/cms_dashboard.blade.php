@extends('cms::layouts.default')

@php
    $stats = [
        ['icon' => 'cash-check', 'color' => 'warning', 'desc' => 'Pembayaran belum diproses'],
        ['icon' => 'ticket-confirmation-outline', 'color' => 'danger', 'desc' => 'Invoice belum lunas'],
        ['icon' => 'account-box-multiple-outline', 'color' => 'primary', 'desc' => 'Jumlah Registrasi Siswa'],
        ['icon' => 'cash-check', 'color' => 'success', 'desc' => 'Jumlah Pembayaran'],
    ];
@endphp

@section('content')
    <div class="row g-3">
        {{-- Sisi Kiri: Welcome Banner & Stats --}}
        <div class="col-lg-8">
            <div class="card border-0 mb-3">
                <div class="card-body d-md-flex align-items-center justify-content-between p-4">
                    <div class="text-center text-md-start">
                        <h2 class="fw-normal">Selamat datang {{ auth()->user()->name }}!</h2>
                        <p class="text-muted mb-0">di Content Management System</p>
                    </div>
                    <img src="{{ asset('img/manypixels/Designer_Flatline.svg') }}" height="160" class="mt-3 mt-md-0">
                </div>
            </div>

            <div class="row g-3">
                @foreach($stats as $item)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar-md bg-soft-{{ $item['color'] }} text-{{ $item['color'] }} rounded p-2">
                                    <i class="mdi mdi-{{ $item['icon'] }} fs-3"></i>
                                </div>
                                <div class="ms-3">
                                    <h4 class="mb-1">0</h4> {{-- Ganti dengan variabel count jika ada --}}
                                    <p class="text-muted mb-0 small">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sisi Kanan: Sidebar Info --}}
        <div class="col-lg-4">
            <div class="card border-0 text-center">
                <div class="card-header bg-transparent border-0 text-start">
                    <i class="mdi mdi-google-classroom me-1"></i> CMS
                </div>
                <div class="card-body py-5">
                    <img src="{{ asset('img/manypixels/Online_report_Flatline.svg') }}" height="140" class="mb-3">
                    <div class="text-muted">Silahkan kelola konten anda</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('cms::layouts.default')

@php
    // Dibuat 3 saja agar grid 4-4-4 atau seimbang, "Jumlah Pembayaran" dibuang
    $stats = [
        ['icon' => 'account-group-outline', 'color' => 'primary', 'title' => 'Siswa Terdaftar', 'count' => 0],
        ['icon' => 'file-document-edit-outline', 'color' => 'info', 'title' => 'Konten Draft', 'count' => 0],
        ['icon' => 'check-decagram-outline', 'color' => 'success', 'title' => 'Konten Terbit', 'count' => 0],
    ];
@endphp

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        {{-- Welcome Header --}}
        <div class="col-12">
            <div class="card border-0 bg-transparent mb-2">
                <div class="card-body p-0">
                    <h3 class="fw-bold text-dark mb-1">Halo, {{ auth()->user()->name }}!</h3>
                    <p class="text-muted">Pantau aktivitas dan kelola konten sistem Anda dari sini.</p>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        @foreach($stats as $item)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="avatar-lg bg-soft-{{ $item['color'] }} text-{{ $item['color'] }} rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 60px; min-width: 60px;">
                            <i class="mdi mdi-{{ $item['icon'] }} fs-2"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="fw-bold mb-0">{{ $item['count'] }}</h2>
                            <p class="text-muted mb-0 fw-medium">{{ $item['title'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Quick Actions / Content Section --}}
        <div class="col-lg-8 mt-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Kelola Konten Terbaru</h5>
                </div>
                <div class="card-body p-4 text-center py-5">
                    {{-- Ganti gambar lama dengan icon clean --}}
                    <div class="mb-4 text-muted opacity-25">
                        <i class="mdi mdi-buffer" style="font-size: 80px;"></i>
                    </div>
                    <p class="text-muted px-md-5">Belum ada aktivitas konten terbaru saat ini. Silahkan mulai membuat atau memperbarui informasi sekolah.</p>
                    <a href="#" class="btn btn-primary px-4 py-2 mt-2 shadow-sm rounded-pill">
                        <i class="mdi mdi-plus mr-1"></i> Buat Konten Baru
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Sidebar --}}
        <div class="col-lg-4 mt-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold mb-3">
                        <i class="mdi mdi-rocket-launch-outline me-2"></i> Siap Beraksi?
                    </h5>
                    <p class="small opacity-75 mb-4">
                        Waktunya memperbarui informasi! Kelola profil sekolah, update data master, atau verifikasi pendaftaran siswa hanya dengan beberapa klik.
                    </p>

                    <div class="mt-auto">
                        <hr class="border-white opacity-25 mb-4">

                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-white rounded-circle text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <i class="mdi mdi-pencil-box-multiple fs-5"></i>
                            </div>
                            <div>
                                <span class="small d-block fw-bold">Konten Menunggu Anda</span>
                                <small class="opacity-75" style="font-size: 11px;">Silakan kelola konten Anda sekarang</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

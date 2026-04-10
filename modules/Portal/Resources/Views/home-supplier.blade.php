@extends('portal::layouts.index')

@section('navtitle', 'Supplier Dashboard')

@section('contents')
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box">
                    <a href="#" class="logo logo-light">
                        <span class="logo-sm"><img src="{{ asset('skote/images/logo-light.svg') }}" height="22"></span>
                        <span class="logo-lg"><img src="{{ asset('skote/images/logo-light.png') }}" height="39"></span>
                    </a>
                </div>
            </div>
            <div class="d-flex">
                @include('layouts.shortcut_menu')
                @include('layouts.nav_name')
            </div>
        </div>
    </header>

    @include('layouts.nav-dashboard')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card overflow-hidden mb-3">
                            <div class="bg-primary-subtle" style="background-color: rgba(85, 110, 230, 0.15) !important;">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-3">
                                            <h5 class="text-primary font-size-14">Halo, {{ Str::before($user->name, ' ') }}!</h5>
                                            <p class="mb-0 font-size-12">Ringkasan Supplier</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end text-end">
                                        <img src="{{ asset('skote/images/profile-img.png') }}" style="height: 60px;">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="avatar-sm profile-user-wid mb-2">
                                            <img src="{{ asset('skote/images/users/avatar-1.jpg') }}" class="img-thumbnail rounded-circle">
                                        </div>
                                        <h5 class="font-size-14 text-truncate mb-1">{{ $user->name }}</h5>
                                        <p class="text-muted mb-0 font-size-12">{{ $total_items }} Produk</p>
                                    </div>
                                    <div class="col-sm-6 text-end align-self-end">
                                        <a href="{{ route('poz::supplierz.adjustment.index') }}" class="btn btn-primary btn-sm">
                                            Input Stok <i class="mdi mdi-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3 font-size-14">Pergerakan Stok</h4>
                                <div style="height: 160px; position: relative;">
                                    <canvas id="stokPie"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mini-stats-wid border-start border-success border-4">
                                    <div class="card-body py-3">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted font-size-13 mb-1">Stok Masuk (+)</p>
                                                <h4 class="mb-0 font-size-18">{{ number_format($stok_masuk) }}</h4>
                                            </div>
                                            <div class="avatar-xs align-self-center">
                                                <span class="avatar-title rounded-circle bg-success-subtle text-success">
                                                    <i class="bx bx-up-arrow-alt font-size-18"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mini-stats-wid border-start border-danger border-4">
                                    <div class="card-body py-3">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted font-size-13 mb-1">Stok Keluar (-)</p>
                                                <h4 class="mb-0 font-size-18 text-danger">{{ number_format($stok_keluar) }}</h4>
                                            </div>
                                            <div class="avatar-xs align-self-center">
                                                <span class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                                    <i class="bx bx-down-arrow-alt font-size-18"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3 font-size-14">Aktivitas Terakhir</h4>
                                <div class="table-responsive" style="max-height: 250px;">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="font-size-12">
                                                <th>Waktu</th>
                                                <th>Produk</th>
                                                <th>Qty</th>
                                                <th>Stat</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-size-12">
                                            @foreach($recent_activities as $act)
                                            <tr>
                                                <td>{{ $act->created_at->format('H:i') }}</td>
                                                <td class="text-truncate" style="max-width: 150px;">{{ $act->product->name ?? '-' }}</td>
                                                <td>{{ abs($act->qty) }}</td>
                                                <td><span class="text-{{ $act->status == 'plus' ? 'success' : 'danger' }} fw-bold">{{ strtoupper($act->status) }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3 font-size-14">Peringkat Barang Keluar</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0 font-size-13">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;">#</th>
                                                <th>Produk</th>
                                                <th>Total Keluar</th>
                                                <th>Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topProducts as $top)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $top->product->name ?? '-' }}</td>
                                                <td>{{ number_format($top->total_qty) }}</td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-info" style="width: {{ $stok_keluar > 0 ? ($top->total_qty / $stok_keluar) * 100 : 0 }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('stokPie').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Masuk', 'Keluar'],
                datasets: [{
                    data: [{{ $stok_masuk }}, {{ $stok_keluar }}],
                    backgroundColor: ['#34c38f', '#f46a6a'],
                    borderWidth: 1,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 10, font: { size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush

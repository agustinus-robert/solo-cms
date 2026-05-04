@extends('acc::layouts.default')

@section('title', 'Dasbor Akuntansi | ')

@section('navtitle', 'Ikhtisar Keuangan')

@php
    $charts = [
        [
            'name' => 'expenseChart',
            'label' => 'Komposisi Biaya (Bulan Ini)',
            'icon' => 'mdi mdi-chart-pie',
            'data' => $expense_stats ?? [],
            'type' => 'doughnut',
            'row' => 6,
            'height' => '35vh',
        ],
        [
            // Contoh data statis jika belum ada query revenue vs expense
            'name' => 'revenueChart',
            'label' => 'Analisis Saldo Akun',
            'icon' => 'mdi mdi-chart-line',
            'data' => ['Kas' => $totalBalance, 'Piutang' => $totalReceivable, 'Hutang' => $totalPayable],
            'type' => 'pie',
            'row' => 6,
            'height' => '35vh',
        ],
    ];
@endphp

@section('content')
    <div class="row">
        {{-- Welcome Card --}}
        <div class="col-xl-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="order-md-first text-md-start text-center">
                            <div class="px-3 py-2">
                                <h2 class="fw-bold text-primary">Halo, {{ Auth::user()->name }}!</h2>
                                <div class="text-muted">Panel Kendali Akuntansi — <strong>{{ now()->translatedFormat('l, d F Y') }}</strong></div>
                            </div>
                        </div>
                        <div>
                            <img src="{{ asset('img/manypixels/Welcome.svg') }}" alt="Welcome" style="height: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-75">Total Kas & Bank</p>
                            <h3 class="fw-bold">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                        </div>
                        <i class="mdi mdi-wallet mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-75">Piutang (Arus Masuk)</p>
                            <h3 class="fw-bold">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</h3>
                        </div>
                        <i class="mdi mdi-arrow-bottom-left-thick mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-75">Hutang (Arus Keluar)</p>
                            <h3 class="fw-bold">Rp {{ number_format($totalPayable, 0, ',', '.') }}</h3>
                        </div>
                        <i class="mdi mdi-arrow-top-right-thick mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        @foreach ($charts as $key => $value)
            <div class="col-md-{{ $value['row'] }} mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body border-bottom fw-bold">
                        <i class="{{ $value['icon'] }} me-2 text-primary"></i> {{ $value['label'] }}
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        @php $totalData = is_array($value['data']) ? array_sum($value['data']) : 0; @endphp
                        @if($totalData > 0)
                            <div class="chart-container" style="height: {{ $value['height'] }}; width:100%;">
                                <canvas id="{{ $value['name'] }}" class="custom-chart"></canvas>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-database-off mdi-48px text-light"></i>
                                <p class="text-muted mt-2">Belum ada data keuangan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Recent Transactions Table --}}
        <div class="col-xl-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-swap-horizontal me-2"></i>Transaksi Jurnal Terakhir</h5>
                    <button class="btn btn-sm btn-light border" onclick="reloadTable()">
                        <i class="mdi mdi-refresh me-1"></i> Perbarui
                    </button>
                </div>
                <div id="table-container">
                    @include('acc::dashboard._table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .chart-container { display: flex; justify-content: center; align-items: center; width: 100%; }
        .custom-chart { max-width: 100%; max-height: 100%; }
        .card { border-radius: 12px; }
        .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartConfigs = {!! json_encode($charts) !!};

        function createChartData(dataValues, label) {
            const labels = Object.keys(dataValues);
            const values = Object.values(dataValues);
            return {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#5a5c69', '#36b9cc'],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            };
        }

        function createChart(ctx, chartData, chartType) {
            return new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: chartType === 'doughnut' ? '70%' : 0
                }
            });
        }

        chartConfigs.forEach(config => {
            const ctx = document.getElementById(config.name);
            if (ctx && config.data && Object.keys(config.data).length > 0) {
                createChart(ctx, createChartData(config.data, config.label), config.type || 'pie');
            }
        });

        function reloadTable() {
            $.ajax({
                url: "{{ route('acc::dashboard') }}",
                type: "GET",
                success: function(data) {
                    $('#table-container').html(data);
                }
            });
        }
    </script>
@endpush

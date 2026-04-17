@extends('hotel::layouts.default')

@section('title', 'Dasbor Hotel | ')

@section('navtitle', 'Dasbor Operasional')

@php
    $charts = [
        [
            'name' => 'roomStatusChart',
            'label' => 'Status Kondisi Kamar',
            'icon' => 'mdi mdi-door-open',
            'data' => $room_stats_chart,
            'type' => 'pie',
            'row' => 4,
            'height' => '35vh',
        ],
        [
            'name' => 'bookingSourceChart',
            'label' => 'Sumber Reservasi',
            'icon' => 'mdi mdi-source-branch',
            'data' => $booking_sources,
            'type' => 'doughnut',
            'row' => 4,
            'height' => '35vh',
        ],
        [
            'name' => 'paymentStatusChart',
            'label' => 'Status Pembayaran (Bulan Ini)',
            'icon' => 'mdi mdi-cash-check',
            'data' => $payment_stats,
            'type' => 'pie',
            'row' => 4,
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
                                <h2 class="fw-bold text-primary">Selamat Datang, {{ Auth::user()->name }}!</h2>
                                <div class="text-muted">Manajemen Hotel & Properti — <strong>{{ now()->translatedFormat('l, d F Y') }}</strong></div>
                            </div>
                        </div>
                        <div>
                            <img src="{{ asset('img/manypixels/Welcome.svg') }}" alt="Welcome" style="height: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Statistics Cards (Key Performance Indicators) --}}
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-75">Tersedia</p>
                            <h3 class="fw-bold">{{ $roomStats['available'] ?? 0 }}</h3>
                        </div>
                        <i class="mdi mdi-check-circle-outline mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-75">Terisi (Occupied)</p>
                            <h3 class="fw-bold">{{ $roomStats['occupied'] ?? 0 }}</h3>
                        </div>
                        <i class="mdi mdi-account-group mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 fw-bold opacity-75">Kotor (Dirty)</p>
                            <h3 class="fw-bold">{{ $roomStats['dirty'] ?? 0 }}</h3>
                        </div>
                        <i class="mdi mdi-broom mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-75">Kedatangan (Arrival)</p>
                            <h3 class="fw-bold">{{ $stats['arrivals'] ?? 0 }}</h3>
                        </div>
                        <i class="mdi mdi-luggage mdi-36px opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Charts Section --}}
        @foreach ($charts as $key => $value)
            <div class="col-md-{{ $value['row'] }} mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body border-bottom fw-bold">
                        <i class="{{ $value['icon'] }} me-2 text-primary"></i> {{ $value['label'] }}
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        @php
                            $totalData = is_array($value['data']) ? array_sum($value['data']) : 0;
                        @endphp

                        @if($totalData > 0)
                            <div class="chart-container" style="height: {{ $value['height'] }}; width:100%;">
                                <canvas id="{{ $value['name'] }}" class="custom-chart"></canvas>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-database-off mdi-48px text-light"></i>
                                <p class="text-muted mt-2">Belum ada data</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Table Kedatangan (Arrivals Today) --}}
        <div class="col-xl-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-clock-outline me-2"></i>Kedatangan Tamu Hari Ini</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tamu</th>
                                <th>Kamar</th>
                                <th>Tipe</th>
                                <th>Status Bayar</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayArrivals ?? [] as $arrival)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $arrival->guest->full_name }}</div>
                                        <small class="text-muted">{{ $arrival->guest->phone_number }}</small>
                                    </td>
                                    <td><span class="badge bg-outline-primary text-primary border border-primary px-3">{{ $arrival->room->room_number }}</span></td>
                                    <td>{{ $arrival->room->type->name }}</td>
                                    <td>
                                        <span class="badge {{ $arrival->payment_status->value == 'paid' ? 'bg-success' : 'bg-soft-warning text-warning' }}">
                                            {{ strtoupper($arrival->payment_status->value) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-primary shadow-sm px-3">Check-in</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Tidak ada kedatangan yang dijadwalkan hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .bg-soft-warning { background-color: #fff3cd; }
        .custom-chart { max-width: 100%; max-height: 100%; }
        .card { border-radius: 12px; }
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
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#5a5c69', '#36b9cc'
                    ],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            };
        }

        function createChart(ctx, chartData, chartType) {
            const isPie = chartType === 'pie' || chartType === 'doughnut';

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
                                font: { size: 11 },
                                generateLabels: (chart) => {
                                    const data = chart.data.datasets[0].data;
                                    const total = data.reduce((a, b) => a + b, 0);
                                    return chart.data.labels.map((label, i) => ({
                                        text: `${label}: ${data[i]} (${((data[i]/total)*100).toFixed(1)}%)`,
                                        fillStyle: chart.data.datasets[0].backgroundColor[i],
                                        index: i
                                    }));
                                }
                            }
                        }
                    },
                    cutout: chartType === 'doughnut' ? '70%' : 0
                }
            });
        }

        chartConfigs.forEach(config => {
            const ctx = document.getElementById(config.name);
            if (ctx && config.data) {
                createChart(ctx, createChartData(config.data, config.label), config.type || 'pie');
            }
        });
    </script>
@endpush

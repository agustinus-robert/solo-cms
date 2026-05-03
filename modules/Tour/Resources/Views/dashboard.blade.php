@extends('tour::layouts.default')

@section('title', 'Dasbor Tour | ')

@section('navtitle', 'Dasbor')

@php
    $charts = [
        [
            'name' => 'locationChart',
            'label' => 'Tour berdasarkan Lokasi',
            'icon' => 'mdi mdi-map-marker-radius',
            'data' => $tour_by_locations,
            'type' => 'pie',
            'row' => 4,
            'height' => '40vh',
        ],
        [
            'name' => 'priceChart',
            'label' => 'Tour berdasarkan Range Harga',
            'icon' => 'mdi mdi-cash-multiple',
            'data' => $tour_by_prices,
            'type' => 'pie',
            'row' => 4,
            'height' => '40vh',
        ],
        [
            'name' => 'availabilityChart',
            'label' => 'Ketersediaan Paket Hari Ini',
            'icon' => 'mdi mdi-calendar-check',
            'data' => $tour_by_availabilities,
            'type' => 'pie',
            'row' => 4,
            'height' => '40vh',
        ],
        [
            'name' => 'labelChart',
            'label' => 'Distribusi Fasilitas (Label)',
            'icon' => 'mdi mdi-tag-multiple-outline',
            'data' => $tour_by_labels,
            'type' => 'bar',
            'row' => 12,
            'height' => '40vh',
        ],
    ];
@endphp

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card border-0">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-between">
                        <div>
                            <img class="w-100" src="{{ asset('img/manypixels/Welcome.svg') }}" alt="" style="height: 140px;">
                        </div>
                        <div class="order-md-first text-md-start text-center">
                            <div class="px-4 py-3">
                                <h2 class="fw-normal">Selamat datang {{ Auth::user()->name }}!</h2>
                                <div class="text-muted">Manajemen Modul Tour</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($charts as $key => $value)
            <div class="col-md-{{ $value['row'] }}">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body border-bottom">
                        <i class="{{ $value['icon'] }} me-2"></i> {{ $value['label'] }}
                    </div>
                    <div class="chart-container" style="height: {{ $value['height'] }}; width:100%;">
                        <canvas id="{{ $value['name'] }}" class="custom-chart"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('styles')
    <style>
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
        }
        .custom-chart {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartsData = {!! json_encode($charts) !!};

        function createChartData(dataValues, label) {
            const colors = [
                'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
            ];

            return {
                labels: Object.keys(dataValues),
                datasets: [{
                    label: label,
                    data: Object.values(dataValues),
                    backgroundColor: colors,
                    borderColor: colors.map(c => c.replace('0.7', '1')),
                    borderWidth: 1
                }]
            };
        }

        function generateLegendLabels(chart) {
            const data = chart.data.datasets[0].data;
            const total = data.reduce((sum, value) => sum + value, 0);

            return chart.data.labels.map((label, index) => {
                const value = data[index];
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                return {
                    text: `${label}: ${value} (${percentage}%)`,
                    fillStyle: chart.data.datasets[0].backgroundColor[index],
                    index: index
                };
            });
        }

        chartsData.forEach(chart => {
            const ctx = document.getElementById(chart.name);
            if (ctx) {
                const config = {
                    type: chart.type,
                    data: createChartData(chart.data, chart.label),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    generateLabels: chart.type === 'pie' ? generateLegendLabels : undefined
                                }
                            }
                        }
                    }
                };
                new Chart(ctx, config);
            }
        });
    </script>
@endpush

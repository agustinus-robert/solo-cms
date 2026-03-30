@extends('hrms::layouts.default')

@section('title', 'Rekapitulasi Tiket | ')
@section('navtitle', 'Rekapitulasi Tiket')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('hrms::service.leave.manage.index')) }}">
            <i class="mdi mdi-arrow-left-circle-outline mdi-36px text-primary"></i>
        </a>
        <div class="ms-4">
            <h2 class="mb-1">Rekapitulasi Tiket Laporan</h2>
            <div class="text-secondary">Periode: <strong>{{ \Carbon\Carbon::parse($start_at)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($end_at)->format('d M Y') }}</strong></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase opacity-75">Total Tiket</h6>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-success">Selesai</h6>
                    <h2 class="mb-0">{{ $stats['done'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-warning">Proses</h6>
                    <h2 class="mb-0">{{ $stats['on_process'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-danger">Belum Selesai</h6>
                    <h2 class="mb-0">{{ $stats['not_done'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="card-title">Persentase Status</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="card-title">
                        Daftar Tiket
                        <small class="text-muted">
                            ({{ \Carbon\Carbon::parse(request('start_at'))->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse(request('end_at'))->format('d M Y') }})
                        </small>
                    </h5>
                </div>

                <div class="card-body p-0">

                    <div style="max-height:400px; overflow-y:auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Kode</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <small class="fw-bold">{{ $ticket->kd }}</small>
                                    </td>

                                    <td>
                                        {{ Str::limit($ticket->title, 30) }}
                                    </td>

                                    <td>
                                        @if($ticket->job_status)
                                            <span class="badge {{ $ticket->job_status->badge() }}">
                                                {{ $ticket->job_status->label() }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
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

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">Detail Seluruh Tiket</h5>

            <div class="table-responsive">
                <div style="max-height:450px; overflow-y:auto;">
                    <table class="table table-striped mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Tgl Dibuat</th>
                                <th>Kode</th>
                                <th>Pengaju</th>
                                <th>Judul Tiket</th>
                                <th>Kategori</th>
                                <th>Status Pekerjaan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->created_at->format('d/m/y H:i') }}</td>

                                <td>
                                    <code>{{ $ticket->kd }}</code>
                                </td>

                                <td>
                                    {{ $ticket->user->name ?? 'System' }}
                                </td>

                                <td>
                                    {{ $ticket->title }}
                                </td>

                                <td>
                                    {{ $ticket->category->name ?? '-' }}
                                </td>

                                <td>
                                    @if($ticket->job_status)
                                        <i class="{{ $ticket->job_status->icon() }} {{ $ticket->job_status->class() }}"></i>
                                        {{ $ticket->job_status->label() }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary">
                                    Tidak ada data tiket ditemukan pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statusChart');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Proses', 'Belum Selesai'],
            datasets: [{
                data: [
                    {{ $stats['done'] }}, 
                    {{ $stats['on_process'] }}, 
                    {{ $stats['not_done'] }}
                ],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
</script>
@endpush
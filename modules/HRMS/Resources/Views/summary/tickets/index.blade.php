@extends('hrms::layouts.default')

@section('title', 'Rekap Pelaporan | ')
@section('navtitle', 'Rekap Pelaporan')

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0">
                <div class="card-body">
                    <i class="mdi mdi-format-list-bulleted"></i> Rekap Pelaporan
                </div>
                {{-- <div class="card-body border-top">
                    <form class="form-block row gy-2 gx-2" action="{{ route('hrms::summary.feastdays.index', request()->all()) }}" method="get">
                        <div class="flex-grow-1 col-auto">
                            <div class="input-group">
                                <div class="input-group-text">Cut off</div>
                                <input class="form-control" type="date" name="cutoff_at" value="{{ $cutoff_at->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-dark"><i class="mdi mdi-magnify"></i> Cari</button>
                        </div>
                    </form>
                </div> --}}
                <div class="table-responsive">
                    <table class="mb-0 table align-middle" style="min-width:900px;">
                        <thead>
                            <tr>
                                <th class="text-center">Kode</th>
                                <th>Judul</th>
                                <th>Dari</th>
                                <th>Tanggal</th>
                                <th>Tujuan</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                            @php($type = \Modules\Support\Enums\TicketTargetableEnum::tryFromInstance(get_class($ticket->targetable)))
                            <tr>
                                <td class="text-center fw-bold">
                                    <a class="text-dark" href="{{ route('support::tickets.show', ['ticket' => $ticket->kd, 'next' => url()->current()]) }}">#{{ $ticket->kd }}</a>
                                </td>
                                <td style="width:200px;">{{ Str::words($ticket->title, 4) }}</td>
                                <td>{{$ticket->user->name}}</td>
                                <td>
                                    {{ $ticket->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                </td>
                                <td>
                                    <strong>{{ data_get($ticket->targetable, $type->getter()) }}</strong> <br>
                                    <span class="text-muted">{{ $type->label() }}</span>
                                </td>
                                <td>
                                    <span class="{{ $ticket->category->class() }}">{{ $ticket->category->label() }}</span>
                                </td>

                                <td>
                                    <span class="badge {{ $ticket->job_status?->badge() ?? '' }}">
                                        <i class="{{ $ticket->job_status?->icon() }}"></i>
                                        {{ $ticket->job_status?->label() }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    @include('components.notfound')
                                    @can('store', Modules\Support\Models\SupportTicket::class)
                                        <div class="mb-4 mb-lg-5 text-center">
                                            <a class="btn btn-soft-danger" href="{{ route('support::tickets.create', ['next' => url()->current()]) }}"><i class="mdi mdi-plus"></i> Buat tiket baru</a>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    {{ $tickets->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0">
                <div class="card-body">
                    <i class="mdi mdi-filter-outline"></i> Filter
                </div>
                
                <div class="card-body border-top">
                    <form class="form-block" action="{{ route('hrms::summary.ticket.index') }}" method="get">
                        {{-- <input class="d-none" type="date" name="cutoff_at" value="{{ $cutoff_at->format('Y-m-d') }}" required> --}}
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <div class="flex-grow-1 col-auto">
                                <div class="input-group">
                                    <button type="button" class="btn btn-light dropdown-toggle" data-daterangepicker="true" data-daterangepicker-start="[name='start_at']" data-daterangepicker-end="[name='end_at']">
                                        <span class="d-inline d-sm-none"><i class="mdi mdi-sort-clock-descending-outline"></i></span>
                                        <span class="d-none d-sm-inline">Rentang waktu</span>
                                    </button>
                                    <input class="form-control" type="date" name="start_at" value="{{ $start_at->format('Y-m-d') }}" required>
                                    <input class="form-control" type="date" name="end_at" value="{{ $end_at->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Pencarian</label>
                            <input class="form-control" name="search" placeholder="Cari nama atau nip ..." value="{{ request('search') }}" />
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-soft-danger" type="submit"><i class="mdi mdi-filter-outline"></i> Terapkan</button>
                            <a class="btn btn-light" href="{{ route('hrms::summary.ticket.index') }}"><i class="mdi mdi-refresh"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            @if(!empty(request('start_at')) && !empty(request('end_at')))
                <div class="card border-0">
                    <div class="card-body">
                        <i class="mdi mdi-cog-outline"></i> Lanjutan
                    </div>
                    <div class="list-group list-group-flush border-top">
                        <a class="list-group-item list-group-item-action py-3" href="{{ route('hrms::summary.ticket.show',  [
                            'start_at' => request('start_at'),
                            'end_at' => request('end_at')
                        ]) }}"><i class="mdi mdi-calendar-account-outline"></i> Lihat Rekap Berdasarkan periode <br /> 
                            <b>{{ \Carbon\Carbon::parse(request('start_at'))->format('d M Y') }}</b> -
                            <b>{{ \Carbon\Carbon::parse(request('end_at'))->format('d M Y') }}</b>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script src="{{ asset('js/vendor/daterangepicker.js') }}"></script>
    <script>
        
    </script>
@endpush
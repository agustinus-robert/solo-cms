@extends('portal::layouts.default')

@section('title', 'Jadwal kerja nonstaf | ')

@php($colors = ['minul' => 'success', 'yuyun' => 'danger', 'topo' => 'warning', 'acep' => 'primary'])

@section('content')
    <div class="d-flex">
        <div class="d-flex flex-grow-1 align-items-center mb-4">
            <a class="text-decoration-none" href="{{ request('next', route('portal::home')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
            <div class="ms-4">
                <h2 class="mb-1">Jadwal kerja nonstaf</h2>
                <div class="text-muted">Kamu bisa cek jadwal kerja nonstaf di sini!</div>
            </div>
        </div>
        <div class="py-2">
            @can('access', \Modules\HRMS\Models\EmployeeContractSchedule::class)
                <a href="{{ route('portal::schedule.nonstaf.create', ['next' => url()->current()]) }}" class="btn btn-soft-primary"><i class="mdi mdi-plus-circle-outline"></i> Tambah jadwal</a>
            @endcan
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center justify-content-between py-2">
                    <div><i class="mdi mdi-calendar-multiselect"></i> Jadwal kerja nonstaf </div>
                    <form class="tg-steps-presence-history" action="{{ route('portal::schedule.nonstaf.index') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input class="form-control" type="month" name="month" value="{{ $month->format('Y-m') }}">
                            <button class="btn btn-dark"><i class="mdi mdi-eye-outline"></i> <span class="d-none d-sm-inline">Tampilkan</span></button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive tg-steps-presence-calendar">
                    @php($daynames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'])
                    <table class="table-bordered calendar mb-0 table">
                        <thead>
                            <tr>
                                @foreach ($daynames as $dayname)
                                    <th class="text-center">{{ $dayname }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php($_month = $month->copy()->startOfMonth())
                            @php($day = 1)
                            @php($totalWeekOfMonth = $_month->dayOfWeek >= 5 && $_month->daysInMonth >= 30 ? 6 : ($_month->dayOfWeek == 0 && $_month->daysInMonth <= 28 ? 4 : 5))
                            @for ($week = 1; $week <= $totalWeekOfMonth; $week++)
                                <tr>
                                    @foreach ($daynames as $dayindex => $dayname)
                                        @php($_date = date('Y-m-d', mktime(0, 0, 0, $_month->month, $day, $_month->year)))
                                        <td class="{{ $_date == date('Y-m-d') ? 'bg-soft-secondary' : '' }}" style="height: 100px; min-height: 100px; min-width: 120px;">
                                            @if ((($week == 1 && $dayindex >= $_month->dayOfWeek) || $week > 1) && ($week != $totalWeekOfMonth || $day <= $_month->daysInMonth))
                                                <div class="position-relative h-100 float-start">
                                                    <div class="d-flex">
                                                        <div class="small flex-grow-1 position-absolute {{ $dayindex == 0 || isset($moments[$_date]) ? 'text-danger' : 'text-muted' }}" style="opacity: .8;">{{ $day }}</div>
                                                        @isset($moments[$_date])
                                                            <div class="small position-absolute text-danger ms-3" data-bs-toggle="tooltip" title="{{ $moments[$_date]->pluck('name')->join(',', ' dan ') }}" style="opacity: .8;"><i class="mdi mdi-information-outline"></i></div>
                                                        @endisset
                                                    </div>
                                                </div>
                                                @foreach ($workshifts as $key => $value)
                                                    @php($x = $results->where('date', $_date)->where('shift', $value->value))
                                                    @php($n = $x->first()->name ?? null)
                                                    <div class="@if ($loop->last) mb-0 @else mb-2 @endif text-{{ !is_null($n) ? $colors[$n] : 'secondary' }} @if ($loop->first) mt-4 @endif me-1 ms-2">
                                                        <div class="row bg-soft-{{ !is_null($n) ? $colors[$n] : 'secondary' }}">
                                                            <div class="col-xl-8">
                                                                <small style="font-size: .75rem;"><i class="mdi mdi-clock-time-five-outline"></i> {{ $x->first()->in ?? '' }} - {{ $x->first()->out ?? '' }} </small>
                                                            </div>
                                                            <div class="col-xl-4 text-lg-start text-center">
                                                                <small class="fw-normal nowrap" style="font-size: .82rem;"> {{ \Str::Ucfirst($n) }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @php($day++)
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        table.calendar>tbody>tr>td:hover {
            background: #fafafa;
        }

        .pulse-soft-danger {
            animation: pulse-soft-danger 1s infinite;
        }

        .pulse-soft-danger:hover {
            animation: none;
        }

        @-webkit-keyframes pulse-soft-danger {
            0% {
                -webkit-box-shadow: 0 0 0 0 rgba(255, 217, 215, .6);
            }

            80% {
                -webkit-box-shadow: 0 0 0 10px rgba(255, 217, 215, 0);
            }

            100% {
                -webkit-box-shadow: 0 0 0 0 rgba(255, 217, 215, 0);
            }
        }

        @keyframes pulse-soft-danger {
            0% {
                -moz-box-shadow: 0 0 0 0 rgba(255, 217, 215, .6);
                box-shadow: 0 0 0 0 rgba(255, 217, 215, .6);
            }

            80% {
                -moz-box-shadow: 0 0 0 10px rgba(255, 217, 215, 0);
                box-shadow: 0 0 0 10px rgba(255, 217, 215, 0);
            }

            100% {
                -moz-box-shadow: 0 0 0 0 rgba(255, 217, 215, 0);
                box-shadow: 0 0 0 0 rgba(255, 217, 215, 0);
            }
        }
    </style>
@endpush

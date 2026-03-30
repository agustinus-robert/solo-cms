@extends('finance::layouts.default')

@section('title', 'Rekapitulasi presensi | ')
@section('navtitle', 'Rekapitulasi presensi')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a class="text-decoration-none" href="{{ request('next', route('portal::recapitulation.teachers.index')) }}"><i class="mdi mdi-arrow-left-circle-outline mdi-36px"></i></a>
        <div class="ms-4">
            <h2 class="mb-1">Ubah rekap pengajaran baru</h2>
            <div class="text-secondary">Anda dapat mengubah rekap pengajaran dengan mengisi formulir di bawah</div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card border-0">
                <div class="card-body">
                    <i class="mdi mdi-history"></i> Riwayat rekap mengajar
                </div>
                <div class="table-responsive border-top" style="overflow: auto; max-height: 960px;">
                    <table class="mb-0 table">
                        <tbody>
                            
                            @php
                                $grouped = collect($scanlogdata ?? []);

                                $totalWfa = $grouped->sum(function ($data) {
                                    return collect($data['presence'] ?? [])
                                        ->where('location.value', 2)
                                        ->count();
                                });

                                $totalWfo = $grouped->filter(function ($data) {
                                    return collect($data['presence'] ?? [])
                                        ->where('location.value', 1)
                                        ->count() > 0;
                                })->count();
                                $original = ['times' => []];
                                $no = 1;
                            @endphp

                            @forelse ($entries as $date => $shifts)
                                @foreach ($shifts as $entry)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>
                                            <span @if ($moment = $moments->firstWhere('date', $date)) data-bs-toggle="tooltip" title="" data-bs-placement="right" data-bs-original-title="{{ $moment->name }}" @endif @class(['fw-bold', 'text-danger' => $moment])>
                                                @php
                                                    $modifier = $entry->modifier ?? null;
                                                    $adjustment = 0;

                                                    if ($modifier !== null) {
                                                        if (str_starts_with($modifier, '+')) {
                                                            $adjustment = floatval($modifier);
                                                        } elseif (str_starts_with($modifier, '-')) {
                                                            $adjustment = floatval($modifier);
                                                        }
                                                    }

                                                    $baseHour = 2 + $adjustment;
                                                @endphp

                                                {{ strftime('%A, %d %b %Y', strtotime($date)) }}
                                                @if ($moment)
                                                    <i class="mdi mdi-information-outline"></i>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @php($original['times'][$date] = $entry->in?->format('H:i:s') ?? null)
                                            @php($currentday = $scanlogs[$date] ?? [])
                                            {{-- {{ implode(', ', array_map(fn($location) => $locations[$location], $entry->location)) }} --}}
                                            @foreach ($entry->location ?? [1] as $k => $v)
                                                @if ($loop->first && $loop->last)
                                                @elseif($loop->last && !$loop->first)
                                                    @php($current = $currentday->where('location', $v))
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $current->first()->created_at->format('H:i:s') }}">
                                                        <span class="text-dark">{{ $locations[$v] }}</span>
                                                        <small>
                                                            <i class="mdi mdi-information-outline text-muted"></i>
                                                        </small>
                                                    </span>
                                                @else
                                                    {{ $locations[$v] }},
                                                @endif
                                            @endforeach

                                            @php($dateWeekEnd = date('w', strtotime($entry->date)))

                                            @if ($dateWeekEnd == 0 || $dateWeekEnd == 6 || $moments->contains('date', $entry->date))
                                                {{ $entry->shift->label() }} {!! request()->user()->employee->position->position->dept_id == 7 ? '' : "<sup><b>Extra</b></sup>" !!}
                                            @else
                                                @if ($entry->shift->value == 5)
                                                    {{ $entry->shift->label() }} {!! request()->user()->employee->position->position->dept_id == 7 ? '' : "<sup><b>Extra</b></sup>" !!}
                                                @else
                                                    {{ $entry->shift->label() }}
                                                @endif
                                            @endif
                                        </td>
                                        <td @class(['text-center'])>
                                            @if ($baseHour < 2)
                                                <span class="badge bg-danger">
                                                    {{ $baseHour }} jam</span>
                                            @elseif($baseHour == 2)
                                                <span class="badge bg-primary">
                                                    {{ $baseHour }} jam</span>
                                            @elseif($baseHour > 2)
                                                <span class="badge bg-success">
                                                    {{ $baseHour }} jam</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td>@include('components.notfound-vertical')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card border-0">
                <div class="card-body">
                    <i class="mdi mdi-plus-circle-outline"></i> Buat rekapitulasi baru
                </div>
                <div class="card-body border-top border-light">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="text-muted">Nama karyawan</div>
                            <div class="fw-bold">{{ $attendance->employee->user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted">
                                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Tanggal pada periode ini akan digunakan untuk penghitungan gaji, jadi pastikan tanggal yang Kamu isi adalah benar">
                                    <span>Periode</span>
                                    <i class="mdi mdi-information-outline"></i>
                                </span>
                            </div>
                            <div class="align-items-center d-flex">
                                <div>{{ $attendance->start_at->format('d-M-Y') }}</div>
                                <div class="text-muted small mx-2">&mdash; s.d. &mdash;</div>
                                <div>{{ $attendance->end_at->format('d-M-Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top border-light">
                    <form class="form-block form-confirm" action="{{ route('finance::summary.teachings.update', ['teaching' => $attendance->empl_id, 'start_at' => $attendance->start_at->format('Y-m-d'), 'end_at' => $attendance->end_at->format('Y-m-d'), 'next' => request('next', route('hrms::summary.attendances.index'))]) }}" method="post"> @csrf
                        <div class="row gy-4">
                            <div class="col-lg-6">
                                <h6 class="fw-bold mb-3">Rekapitulasi umum periode ini</h6>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jumlah hari</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="summary[days]" value="{{ $attendance->result->days }}">
                                            <div class="input-group-text">hari</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Hari efektif</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="summary[workdays]" value="{{ $attendance->result->workdays }}">
                                            <div class="input-group-text">hari</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Hari libur nasional</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="summary[holidays]" value="{{ $attendance->result->holidays }}">
                                            <div class="input-group-text">hari</div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h6 class="fw-bold mb-3">Rekapitulasi perizinan</h6>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jumlah izin</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="summary[unpresence][leave]" value="{{ $attendance->result->unpresence->leave }}">
                                            <div class="input-group-text">hari &nbsp;<i class="mdi mdi-information-outline text-primary" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#leaveDatesModal" id="openLeaveDates" title="Cek tanggal Izin"></i></div>
                                        </div>
                                    </div>
                                </div>
                                @foreach (Modules\Core\Enums\VacationTypeEnum::cases() as $type)
                                    <div class="row align-items-center mb-2">
                                        <label class="col-form-label col-md-4" for="">Jumlah {{ $type->label() }}</label>
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                <input class="form-control" type="number" min="0" step="0.1" name="summary[unpresence][vacation][{{ strtolower($type->name) }}]" value="{{ $attendance->result->unpresence->vacation->{strtolower($type->name)} }}">
                                                <div class="input-group-text">hari &nbsp;<i onclick="modalVacation('vacation{{ $type->value }}')" style="cursor:pointer;" class="mdi mdi-information-outline text-primary" title="Cek tanggal {{ $type->label() }}"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-lg-6">
                                <h6 class="fw-bold mb-3">Rekapitulasi Kehadiran Mengajar</h6>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jam reguler</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="summary[attendance_work]" value="{{ $attendance->result->attendance_work }}">
                                            <div class="input-group-text">kehadiran</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jam ekstra</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="1" name="summary[additional_workdays]" value="{{ $attendance->result->additional_workdays }}">
                                            <div class="input-group-text">kehadiran</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jumlah mengajar</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="summary[attendance_total]" value="{{ $attendance->result->attendance_total }}">
                                            <div class="input-group-text">kehadiran</div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4">Mengajar WFO</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control"
                                                type="number"
                                                min="0"
                                                step="0.1"
                                                name="summary[presence][wfo]"
                                                value="{{ $totalWfo }}">
                                            <div class="input-group-text">kehadiran</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4">Mengajar WFA</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control"
                                                type="number"
                                                min="0"
                                                step="0.1"
                                                name="summary[presence][wfa]"
                                                value="{{ $totalWfa }}">
                                            <div class="input-group-text">kehadiran</div>
                                        </div>
                                    </div>
                                </div>

                                @include('finance::summary.teacher.modal.scanlog')   
                                @include('finance::summary.teacher.modal.job')
                            </div>
                        </div>
                        <div class="row">
                            <h6 class="fw-bold mb-3">Jam Mengajar</h6>

                            <div class="col-lg-6">
                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">jam mengajar reguler</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="teach[amount_total]" value="{{ round($teach->result->amount_total, 1) }}">
                                            <div class="input-group-text">Jam</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jam Kerja Kelebihan</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="teach[overhour]" value="{{ round($teach->result->overhour, 1) }}">
                                            <div class="input-group-text">Jam</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-center mb-2">
                                    <label class="col-form-label col-md-4" for="">Jam Kerja Extra</label>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input class="form-control" type="number" min="0" step="0.1" name="teach[extrahour]" value="{{ round($teach->result->extrahour, 1) }}">
                                            <div class="input-group-text">Jam</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        @if ($userNow->position_id !== 3)
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="agreement" type="checkbox" required>
                                <label class="form-check-label" for="agreement">Dengan ini saya selaku {{ Auth::user()->employee->position->position->name ?? 'Human Resource (HR)' }} menyatakan data di atas adalah valid</label>
                            </div>

                            <div>
                                <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                                <a class="btn btn-ghost-light text-dark" href="{{ request('next', route('portal::recapitulation.teachers.index')) }}"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                            </div>
                            <input class="position-fixed d-none" type="hidden" name="original" value='{{ json_encode($original) }}'>
                        @endif
                    </form>

                    @if ($userNow->position_id == 3)
                        <div class="row">
                            <div class="col-md-6">
                                @foreach ($attendance->approvables->take(1) as $approvable)
                                    <div class="row gy-2 @if (!$loop->last) mb-4 @endif">
                                        <div class="col-md-12">
                                            <div class="text-muted small mb-1">
                                                {{ ucfirst($approvable->type) }} #{{ $approvable->level }}
                                            </div>
                                            <strong>{{ $approvable->userable->getApproverLabel() }}</strong>
                                        </div>
                                        <div class="col-md-12">
                                            @if ($approvable->userable->is($userNow) && !$attendance->trashed())
                                                <form class="form-block" action="{{ route('finance::summary.summary.permission', ['next' => request('next', route('finance::summary.teachings.index'))]) }}" method="post"> @csrf @method('PUT')
                                                    <input type="hidden" name="id_attendance" value="{{ $attendance->id }}" />
                                                    <input type="hidden" name="id_teaching" value="{{ $teach->id }}" />
                                                    <div class="mb-3">
                                                        <select class="form-select @error('result') is-invalid @enderror" name="result">
                                                            @foreach ($results as $result)
                                                                @unless (($approvable->cancelable && in_array($result, Modules\HRMS\Models\EmployeeRecapSubmission::$cancelable_disable_result)) || in_array($result, Modules\HRMS\Models\EmployeeRecapSubmission::$approvable_disable_result ?? []))
                                                                    <option value="{{ $result->value }}" @selected($result->value == old('result', $approvable->result->value))>{{ $result->label() }}</option>
                                                                @endunless
                                                            @endforeach
                                                        </select>
                                                        @error('result')
                                                            <div class="text-danger small">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <textarea class="form-control @error('reason') is-invalid @enderror" type="text" name="reason" placeholder="Alasan ...">{{ old('reason', $approvable->reason) }}</textarea>
                                                        @error('reason')
                                                            <div class="text-danger small">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <button class="btn btn-soft-danger"><i class="mdi mdi-check"></i> Simpan</button>
                                                    <a class="btn btn-soft-secondary text-dark" href="{{ request('next', route('portal::schedule.submission.index')) }}"><i class="mdi mdi-arrow-left-circle-outline"></i> Kembali</a>
                                                </form>
                                            @else
                                                <div class="h-100 d-flex">
                                                    <div class="align-self-center badge bg-{{ $approvable->result->color() }} fw-normal text-white"><i class="{{ $approvable->result->icon() }}"></i> {{ $approvable->result->label() }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($approvable->history)
                                        <div class="row p-0">
                                            <div class="col-md-6 offset-md-6">
                                                <hr class="text-muted mt-0">
                                                <p class="small text-muted mb-1">Catatan riwayat sebelumnya</p>
                                                {{ $approvable->history->reason }}
                                            </div>
                                        </div>
                                    @endif
                                    @if (!$loop->last)
                                        <hr class="text-muted">
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@include('administration::summary.modal.leave')
@include('administration::summary.modal.vacation')

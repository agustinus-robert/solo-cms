@if ($isTeacher == true)
    <form class="form-block" action="{{ route('portal::attendance.presence.index') }}" method="post"> 
        @csrf

        @if(request()->position == 'employee')
            <input
                class="d-block form-scan form-control mx-auto mb-2 text-center"
                type="{{ in_array(auth()->user()->employee->id,[27,46,53]) ? 'text':'hidden' }}"
                name="latlong"
                value=""
            >
            <input type="hidden" name="position" value="employee" />
            <input type="hidden" name="location" value="{{ $location->value }}">

            <div id="status-text" class="text-center mb-2 fw-bold"></div>

            <button
                id="btn-scan-employee"
                class="btn btn-soft-secondary disabled rounded-circle form-scan mx-auto mb-4"
                style="width:100px;height:100px;"
                type="submit"
                name="submit"
                {{ $alreadyTeacherPresence ? 'disabled' : '' }}
            >
                <i class="mdi mdi-fingerprint mdi-48px"></i>
            </button>
            
            <div id="geolocation-notice"></div>
            {{-- @if($alreadyTeacherPresence)
                <div class="text-success text-center fw-bold">Sesi ini kamu sudah absen!</div>
            @endif --}}
        @else 
            @if($activeShift)
                <div class="alert alert-info mb-3 text-center">
                    <strong>Sesi Aktif: {{ $activeShift->label() }}</strong><br>
                    <small>Waktu Sesi: {{ $activeShift->activeStartTime()[0] }} - {{ $activeShift->activeEndTime()[0] }}</small><br>
                    <small class="text-dark fw-bold">
                        (Presensi berakhir jam: {{ $activeShift->activeEndTime()[0] }})
                    </small>

                    @if($alreadyPresence)
                        <div class="mt-2 text-success fw-bold">
                            <i class="mdi mdi-check-circle"></i> Anda sudah presensi
                        </div>
                    @endif

                    @if($transfer && now()->between(\Carbon\Carbon::parse($transfer->start_date), \Carbon\Carbon::parse($transfer->end_date)))
                        <div class="alert alert-primary mt-2 mb-0 small">
                            <strong>Transfer Aktif:</strong> Guru <b>{{ $transfer->from->label() }}</b> ke <b>{{ $transfer->to->label() }}</b>
                        </div>
                    @endif
                </div>
            @elseif(isset($nextShift))
                <div class="alert alert-warning mb-3">
                    <strong>Tunggu {{ $nextShift->label() }}</strong><br>
                    <small>Waktu: {{ $nextShift->activeStartTime()[0] }} - {{ $nextShift->activeEndTime()[0] }}</small>
                </div>
            @else
                <div class="alert alert-danger mb-3">
                    Semua sesi hari ini sudah selesai.
                </div>
            @endif
            
            <input
                class="d-block form-scan form-control mx-auto mb-2 text-center"
                type="{{ in_array(auth()->user()->employee->id,[27,46,53]) ? 'text':'hidden' }}"
                name="latlong"
                value=""
            >

            <input type="hidden" name="location" value="{{ $location->value }}">

            @if (in_array(auth()->user()->employee->id,[46,53])) @endif

            <button
                class="btn btn-soft-secondary disabled rounded-circle form-scan mx-auto mb-4"
                {{ !$activeShift || $alreadyPresence ? 'btn-soft-secondary disabled' : 'btn-soft-danger' }}"
                style="width:100px;height:100px;"
                {{ !$activeShift ? 'disabled':'' }}
                type="submit"
                name="submit"
            >
                <i class="mdi mdi-fingerprint mdi-48px"></i>
            </button>

            {{-- @if(!$activeShift && !isset($nextShift))
                <div class="text-warning text-center">
                    Belum masuk jam sesi atau waktu aktif presensi sudah habis.
                </div>
            @endif --}}

            <div id="geolocation-notice" class="text-danger text-center">
                Biar bisa presensi,<br> kamu wajib ngaktifin lokasi browser kamu!
            </div>
        @endif
    </form>
@endif
@if (!empty($scanlogdata) && count($scanlogdata) > 0)
<div class="row mb-3">
    <div class="col-xl-12">
        <div class="card border-0 shadow-sm"
             style="cursor:pointer;transition:.3s"
             onmouseover="this.style.backgroundColor='#f8f9fa';this.classList.add('shadow')"
             onmouseout="this.style.backgroundColor='#fff';this.classList.remove('shadow')"
             data-bs-toggle="modal"
             data-bs-target="#modalRiwayatPresensi">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="mdi mdi-history text-primary me-2"></i>
                        <strong>Absensi & Presensi</strong>
                    </div>
                    <div class="text-muted small">
                        <i class="mdi mdi-cursor-default-click-outline me-1"></i>
                        Klik untuk Lihat Detail
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="modalRiwayatPresensi" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white">
                <h5 class="modal-title fw-bold">Detail Log Presensi & Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                @php
                    $regulerLogsGrouped = collect();
                    $assignmentLogsGrouped = collect();

                    $ranges = collect();
                    if(!empty($transfer)) {
                        $transferItems = is_iterable($transfer) ? $transfer : [$transfer];
                        foreach($transferItems as $t) {
                            if(!empty($t->start_date) && !empty($t->end_date)) {
                                $ranges->push([
                                    'start'=> \Carbon\Carbon::parse($t->start_date)->startOfDay(),
                                    'end'  => \Carbon\Carbon::parse($t->end_date)->endOfDay()
                                ]);
                            }
                        }
                    }

                    foreach ($scanlogdata as $dateStr => $data) {
                        $formattedDate = \Carbon\Carbon::parse($dateStr)->translatedFormat('d F Y');
                        $checkTime = \Carbon\Carbon::parse($dateStr);
                        
                        $isAssignment = $ranges->contains(fn($r) => $checkTime->between($r['start'], $r['end']));

                        $itemData = [
                            'absence' => collect($data['absence'] ?? []),
                            'presence' => collect($data['presence'] ?? [])
                        ];

                        if($isAssignment) {
                            $assignmentLogsGrouped->put($formattedDate, $itemData);
                        } else {
                            $regulerLogsGrouped->put($formattedDate, $itemData);
                        }
                    }
                @endphp

                <div class="row">
                    {{-- PANEL KIRI: REGULER --}}
                    <div class="col-md-6 border-end">
                        <h6 class="text-center mb-3 text-primary fw-bold uppercase">Scan Log (Reguler)</h6>
                        <div class="table-responsive" style="max-height:500px; overflow-y:auto; border:1px solid #eee; border-radius:8px;">
                            <table class="table table-bordered align-middle mb-0">
                                @forelse ($regulerLogsGrouped as $date => $logs)
                                    @php
                                        $allReg = $logs['absence']->concat($logs['presence']);
                                        $hasWfoReg = $allReg->where('location.value', 1)->count() > 0;
                                        $countWfaReg = $allReg->where('location.value', 2)->count();
                                    @endphp
                                    <thead class="table-light sticky-top">
                                        <tr><th colspan="2" class="ps-3 fw-bold small text-dark">{{ $date }}</th></tr>
                                    </thead>
                                    <tbody>
                                        {{-- Row Absensi --}}
                                        <tr class="bg-light"><td colspan="2" class="py-1 ps-3 small fw-bold text-muted">Absensi</td></tr>
                                        @if($logs['absence']->isEmpty())
                                            <tr><td colspan="2" class="ps-4 text-danger small italic">Belum absen</td></tr>
                                        @else
                                            @foreach($logs['absence'] as $abs)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="text-muted small me-2">Jam:</span>
                                                        <strong>{{ $abs?->created_at ? \Carbon\Carbon::parse($abs->created_at)->format('H:i') : '-' }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $abs?->location?->value == 1 ? 'bg-primary' : 'bg-danger' }}">
                                                            {{ $abs?->location?->value == 1 ? 'WFO' : 'WFA' }}
                                                        </span>
                                                    </td>                                                
                                                </tr>
                                            @endforeach
                                        @endif

                                        {{-- Row Presensi --}}
                                        <tr class="bg-light"><td colspan="2" class="py-1 ps-3 small fw-bold text-muted">Presensi Mengajar</td></tr>
                                        @if($logs['presence']->isEmpty())
                                            <tr><td colspan="2" class="ps-4 text-danger small italic">Belum presensi</td></tr>
                                        @else
                                            @foreach($logs['presence'] as $pre)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-secondary me-2">Sesi {{ $loop->iteration }}</span>
                                                        <strong>{{ \Carbon\Carbon::parse($pre->created_at)->format('H:i') }}</strong>
                                                    </td>
                                                    <td class="text-center"><span class="badge {{ $pre->location->value == 1 ? 'bg-primary' : 'bg-danger' }}">{{ $pre->location->value == 1 ? 'WFO' : 'WFA' }}</span></td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        {{-- FOOTER PER TANGGAL --}}
                                        <tr class="table-info border-top-2">
                                            <td colspan="2" class="p-2">
                                                <div class="d-flex justify-content-between align-items-center small ps-2 pe-2">
                                                    <div>
                                                        @if($hasWfoReg)
                                                            <span class="text-success fw-bold"><i class="mdi mdi-check-circle"></i> Dapat Tunjangan Transport dan makan 1x</span>
                                                        @else
                                                            <span class="text-muted italic">Tidak mendapatkan tunjangan transport dan makan</span>
                                                        @endif
                                                    </div>
                                                    <div class="fw-bold text-primary">Total WFA: {{ $countWfaReg }}x <br><b>(Dihitung dari presensi mengajar)</b></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <tr><td class="text-center py-5 text-muted small italic">Tidak ada riwayat reguler</td></tr>
                                @endforelse
                            </table>
                        </div>
                    </div>

                    {{-- PANEL KANAN: PENUGASAN --}}
                    <div class="col-md-6">
                        <h6 class="text-center mb-3 text-success fw-bold uppercase">Scan Log (Penugasan)</h6>
                        <div class="table-responsive" style="max-height:500px; overflow-y:auto; border:1px solid #eee; border-radius:8px;">
                            <table class="table table-bordered align-middle mb-0">
                                @forelse ($assignmentLogsGrouped as $date => $logs)
                                    @php
                                        $allAss = $logs['absence']->concat($logs['presence']);
                                        $hasWfoAss = $allAss->where('location.value', 1)->count() > 0;
                                        $countWfaAss = $allAss->where('location.value', 2)->count();
                                    @endphp
                                    <thead class="table-light sticky-top">
                                        <tr><th colspan="2" class="ps-3 fw-bold small text-dark">{{ $date }}</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-light"><td colspan="2" class="py-1 ps-3 small fw-bold text-muted">ABSENSI PULANG</td></tr>
                                        @if($logs['absence']->isEmpty())
                                            <tr><td colspan="2" class="ps-4 text-danger small italic">Belum absen</td></tr>
                                        @else
                                            @foreach($logs['absence'] as $abs)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="text-muted small me-2">Jam:</span>
                                                        <strong>{{ \Carbon\Carbon::parse($abs->created_at)->format('H:i') }}</strong>
                                                    </td>
                                                    <td class="text-center"><span class="badge {{ $abs->location->value == 1 ? 'bg-primary' : 'bg-danger' }}">{{ $abs->location->value == 1 ? 'WFO' : 'WFA' }}</span></td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        <tr class="bg-light"><td colspan="2" class="py-1 ps-3 small fw-bold text-muted">PRESENSI MENGAJAR</td></tr>
                                        @if($logs['presence']->isEmpty())
                                            <tr><td colspan="2" class="ps-4 text-danger small italic">Belum presensi</td></tr>
                                        @else
                                            @foreach($logs['presence'] as $pre)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="badge bg-secondary me-2">Sesi {{ $loop->iteration }}</span>
                                                        <strong>{{ \Carbon\Carbon::parse($pre->created_at)->format('H:i') }}</strong>
                                                    </td>
                                                    <td class="text-center"><span class="badge {{ $pre->location->value == 1 ? 'bg-primary' : 'bg-danger' }}">{{ $pre->location->value == 1 ? 'WFO' : 'WFA' }}</span></td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        <tr class="table-success border-top-2">
                                            <td colspan="2" class="p-2">
                                                <div class="d-flex justify-content-between align-items-center small ps-2 pe-2">
                                                    <div>
                                                        @if($hasWfoAss)
                                                            <span class="text-dark fw-bold"><i class="mdi mdi-check-circle text-success"></i> Dapat Tunjangan Transport dan makan 1x</span>
                                                        @else
                                                            <span class="text-muted italic">Tidak mendapatkan tunjangan transport dan makan</span>
                                                        @endif
                                                    </div>
                                                    <div class="fw-bold text-success">Total WFA: {{ $countWfaAss }}x</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <tr><td class="text-center py-5 text-muted small italic">Tidak ada riwayat penugasan</td></tr>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light p-2">
                <button class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
    const modal=document.getElementById('modalRiwayatPresensi');
    if(modal){
        document.body.appendChild(modal);
    }
});
</script>
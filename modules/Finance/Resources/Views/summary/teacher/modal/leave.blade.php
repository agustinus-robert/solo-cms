<div class="modal fade" id="leaveDatesModal" tabindex="-1" aria-labelledby="leaveDatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="leaveDatesModalLabel">
                    <i class="mdi mdi-calendar-clock text-primary me-2"></i>Rincian Izin & Penugasan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                @php
                    $regularLeaves = [];
                    $assignmentLeaves = [];

                    if(!empty($employeeLeaves)) {
                        foreach ($employeeLeaves as $value) {
                            $datesArray = json_decode($value->dates, true);
                            if(is_array($datesArray)) {
                                foreach ($datesArray as $dateItem) {
                                    $dDate = \Carbon\Carbon::parse($dateItem['d']);
                                    $dDateStr = $dDate->format('Y-m-d');
                                    
                                    $isTransferred = false;
                                    $transferLabel = '';

                                    if(!empty($transfer)){
                                        $start = \Carbon\Carbon::parse($transfer->start_date)->format('Y-m-d');
                                        $end   = \Carbon\Carbon::parse($transfer->end_date)->format('Y-m-d');
                                        
                                        if($dDateStr >= $start && $dDateStr <= $end) {
                                            $isTransferred = true;
                                            $transferLabel = $transfer->from->label() . ' - ' . $transfer->to->label();
                                        }
                                    }

                                    $dataObj = (object)[
                                        'date' => $dDate,
                                        'ctg_id' => $value->ctg_id,
                                        'transfer_label' => $transferLabel
                                    ];

                                    if ($isTransferred) {
                                        $assignmentLeaves[] = $dataObj;
                                    } else {
                                        $regularLeaves[] = $dataObj;
                                    }
                                }
                            }
                        }
                    }
                @endphp

                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-center mb-3 text-primary"><i class="mdi mdi-account-clock me-1"></i> Izin <b>(Reguler)</b></h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px;">
                            <table class="table table-hover table-sm text-center align-middle mb-0">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="py-2">Tanggal</th>
                                        <th class="py-2">Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($regularLeaves as $reg)
                                        <tr>
                                            <td class="py-2">
                                                <strong>{{ $reg->date->translatedFormat('d/m/Y') }}</strong>
                                                <div class="small text-muted text-capitalize">{{ $reg->date->translatedFormat('l') }}</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $reg->ctg_id == 4 ? 'bg-danger' : 'bg-primary' }} px-3">
                                                    {{ $reg->ctg_id == 4 ? 'Sakit' : 'Izin' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-5 text-muted small">
                                                <i class="mdi mdi-information-outline d-block mb-1" style="font-size: 20px;"></i>
                                                Data izin reguler kosong
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-center mb-3 text-success"><i class="mdi mdi-briefcase-check me-1"></i> Izin <b>(Penugasan)</b></h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px;">
                            <table class="table table-hover table-sm text-center align-middle mb-0">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="py-2">Tanggal</th>
                                        <th class="py-2">Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($assignmentLeaves as $assign)
                                        <tr>
                                            <td class="py-2">
                                                <strong>{{ $assign->date->translatedFormat('d/m/Y') }}</strong>
                                                <div class="small text-muted text-capitalize">{{ $assign->date->translatedFormat('l') }}</div>
                                            </td>
                                            <td class="px-2">
                                                <span class="badge bg-soft-info text-info border border-info">
                                                    <i class="mdi mdi-swap-horizontal me-1"></i> {{ $assign->transfer_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-5 text-muted small">
                                                <i class="mdi mdi-information-outline d-block mb-1" style="font-size: 20px;"></i>
                                                Data penugasan kosong
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
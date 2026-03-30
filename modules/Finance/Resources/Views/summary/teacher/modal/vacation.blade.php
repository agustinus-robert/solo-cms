@push('scripts')
    <script>
        function modalVacation(id) {
            const modal = new bootstrap.Modal(document.getElementById(id));
            modal.show();
        }
    </script>
@endpush

@foreach (Modules\Core\Enums\VacationTypeEnum::cases() as $type)
    @php
        $vacations = $employeeVacations->where(fn($vac) => $vac->quota?->category?->type->value === $type->value);
        
        $regularVacations = [];
        $assignmentVacations = [];

        foreach ($vacations as $vac) {
            $dates = json_decode($vac->dates, true);
            if (is_array($dates)) {
                foreach ($dates as $dateItem) {
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
                        'transfer_label' => $transferLabel
                    ];

                    if ($isTransferred) {
                        $assignmentVacations[] = $dataObj;
                    } else {
                        $regularVacations[] = $dataObj;
                    }
                }
            }
        }
    @endphp

    <div class="modal fade" id="vacation{{ $type->value }}" tabindex="-1" aria-labelledby="vacationLabel{{ $type->value }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="vacationLabel{{ $type->value }}">
                        <i class="mdi mdi-clock-outline text-primary me-2"></i>Rincian {{ $type->label() }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="text-center mb-3 text-primary">Cuti <b>(Reguler)</b></h6>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px;">
                                <table class="table table-hover table-sm text-center align-middle mb-0">
                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th class="py-2">Tanggal</th>
                                            <th class="py-2">Tipe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($regularVacations as $reg)
                                            <tr>
                                                <td class="py-2">
                                                    <strong>{{ $reg->date->translatedFormat('d F Y') }}</strong>
                                                    <div class="small text-muted">{{ $reg->date->translatedFormat('l') }}</div>
                                                </td>
                                                <td><span class="badge bg-primary">Reguler</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="py-4 text-muted small">Tidak ada cuti reguler</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-center mb-3 text-success">Cuti <b>(Penugasan)</b></h6>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px;">
                                <table class="table table-hover table-sm text-center align-middle mb-0">
                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th class="py-2">Tanggal</th>
                                            <th class="py-2">Tipe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assignmentVacations as $assign)
                                            <tr>
                                                <td class="py-2">
                                                    <strong>{{ $assign->date->translatedFormat('d F Y') }}</strong>
                                                    <div class="small text-muted">{{ $assign->date->translatedFormat('l') }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info text-dark">
                                                        <i class="mdi mdi-map-marker me-1"></i> {{ $assign->transfer_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="py-4 text-muted small">Tidak ada cuti di periode penugasan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
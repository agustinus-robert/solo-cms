@if ($vacation->trashed())
    <div class="badge bg-soft-danger text-danger fw-normal"><i class="mdi mdi-trash-can-outline"></i> Dihapus</div>
@else
    @php
        $today = date('Y-m-d');
        $results = $vacation->approvables->map(fn($a) => (int) ($a->result->value ?? $a->result));

        $isRejectedAny = $results->contains(2);
        $isRevisionAny = $results->contains(3);
        $isApprovedAll = $results->isNotEmpty() && $results->every(fn($val) => $val === 1);
        $isPast = collect($vacation->dates)->max('d') < $today;
    @endphp

    @if ($isRejectedAny)
        <div class="badge bg-soft-danger text-danger fw-normal"><i class="mdi mdi-close-circle-outline"></i> Pengajuan ditolak</div>
    @elseif ($isRevisionAny)
        <div class="badge bg-soft-warning text-warning fw-normal"><i class="mdi mdi-alert-circle-outline"></i> Butuh Revisi</div>

    @elseif ($isApprovedAll)
        @if ($isPast)
            <div class="badge bg-soft-secondary text-dark fw-normal"><i class="mdi mdi-check"></i> Selesai</div>
        @else
            <div class="badge bg-soft-success text-success fw-normal"><i class="mdi mdi-check-all"></i> Disetujui</div>
        @endif

    @else
        <div class="badge bg-soft-primary text-primary fw-normal"><i class="mdi mdi-timer-sand"></i> Menunggu Persetujuan</div>
    @endif
@endif

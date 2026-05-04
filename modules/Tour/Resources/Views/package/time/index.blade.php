@extends('tour::layouts.default')

@section('title', 'Jadwal Keberangkatan | ')

@section('navtitle', 'Jadwal: ' . $package->package_name)

@section('content')
<div class="row">
    {{-- Form Upsert --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold" id="form-title">Tambah Jadwal</div>
            <div class="card-body">
                <form action="{{ route('tour::package.times.store', $package->id) }}" method="POST" id="upsert-form">
                    @csrf
                    <input type="hidden" name="id" id="time-id">

                    <div class="mb-3">
                        <label class="form-label">Lokasi Penjemputan</label>
                        <select name="tour_location_id" id="location-id" class="form-select" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jam Keberangkatan</label>
                        <input type="time" name="departure_time" id="departure-time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meeting Point (Opsional)</label>
                        <textarea name="meeting_point" id="meeting-point" class="form-control" rows="2" placeholder="Contoh: Lobby Hotel atau Dermaga..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                        <button type="button" id="btn-reset" class="btn btn-light border" style="display: none;" onclick="resetForm()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Lokasi</th>
                            <th>Jam</th>
                            <th>Meeting Point</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($package->times as $time)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $time->location->name }}</td>
                            <td><span class="badge bg-soft-primary text-primary">{{ $time->formatted_time }}</span></td>
                            <td class="small text-muted">{{ $time->meeting_point ?? '-' }}</td>
                            <td class="text-end pe-3">
                                <div class="btn-group shadow-sm">
                                    <button type="button" class="btn btn-sm btn-white border"
                                            onclick="editTime('{{ $time->id }}', '{{ $time->tour_location_id }}', '{{ $time->departure_time }}', '{{ $time->meeting_point }}')">
                                        <i class="mdi mdi-pencil text-warning"></i>
                                    </button>
                                    <button type="button" onclick="deleteAction('{{ route('tour::package.times.destroy', $time->id) }}')" class="btn btn-sm btn-white border text-danger">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada jadwal keberangkatan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function editTime(id, locationId, time, meetingPoint) {
        document.getElementById('form-title').innerText = 'Edit Jadwal';
        document.getElementById('time-id').value = id;
        document.getElementById('location-id').value = locationId;
        document.getElementById('departure-time').value = time.substring(0, 5); // Ambil HH:mm
        document.getElementById('meeting-point').value = meetingPoint === 'null' ? '' : meetingPoint;
        document.getElementById('btn-reset').style.display = 'block';
    }

    function resetForm() {
        document.getElementById('form-title').innerText = 'Tambah Jadwal';
        document.getElementById('upsert-form').reset();
        document.getElementById('time-id').value = '';
        document.getElementById('btn-reset').style.display = 'none';
    }

    function deleteAction(url) {
        if (confirm('Hapus jadwal ini?')) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endpush

@extends('web::electro.index')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 text-primary">
                    <h5 class="fw-bold mb-0">Daftar Alamat Pengiriman</h5>
                    <button class="btn btn-primary shadow-sm" onclick="openUpsertModal()">+ Tambah Alamat</button>
                </div>

                <div class="row">
                    @foreach($addresses as $addr)
                    <div class="col-md-6 mb-3">
                        <div class="card {{ $addr->is_main ? 'border-primary bg-light' : 'border' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="badge {{ $addr->is_main ? 'bg-primary' : 'bg-secondary' }} mb-2">{{ $addr->label }}</span>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-dark" onclick='openUpsertModal(@json($addr))'>Edit</button>
                                        @if(!$addr->is_main)
                                        <form action="{{ route('electro.address.destroy', $addr->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $addr->receiver_name }}</h6>
                                <p class="mb-0 small text-dark">{{ $addr->phone }}</p>
                                <p class="mb-0 small text-muted mt-2">{{ $addr->address }}, RT {{ $addr->rt }}/RW {{ $addr->rw }}, {{ $addr->village }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="upsertModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="upsertForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalTitle">Form Alamat</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-2">
                    <label class="small fw-bold">Label</label>
                    <input type="text" name="label" id="in_label" class="form-control" placeholder="Rumah / Kantor" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="small fw-bold">Penerima</label>
                        <input type="text" name="receiver_name" id="in_receiver" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold">No. WhatsApp</label>
                        <input type="text" name="phone" id="in_phone" class="form-control" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold">Alamat</label>
                    <textarea name="address" id="in_address" class="form-control" rows="2" required></textarea>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-3"><input type="text" name="rt" id="in_rt" class="form-control" placeholder="RT"></div>
                    <div class="col-3"><input type="text" name="rw" id="in_rw" class="form-control" placeholder="RW"></div>
                    <div class="col-6"><input type="text" name="village" id="in_village" class="form-control" placeholder="Desa/Kelurahan"></div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_main" id="in_main" value="1">
                    <label class="form-check-label small" for="in_main">Jadikan Alamat Utama</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100 py-2">Simpan Alamat</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpsertModal(data = null) {
    const form = document.getElementById('upsertForm');
    const modal = new bootstrap.Modal(document.getElementById('upsertModal'));

    if (data) {
        document.getElementById('modalTitle').innerText = 'Edit Alamat';
        form.action = `/electro/address/upsert/${data.id}`;
        document.getElementById('in_label').value = data.label;
        document.getElementById('in_receiver').value = data.receiver_name;
        document.getElementById('in_phone').value = data.phone;
        document.getElementById('in_address').value = data.address;
        document.getElementById('in_rt').value = data.rt;
        document.getElementById('in_rw').value = data.rw;
        document.getElementById('in_village').value = data.village;
        document.getElementById('in_main').checked = !!data.is_main;
    } else {
        document.getElementById('modalTitle').innerText = 'Tambah Alamat';
        form.action = "{{ route('electro.address.upsert') }}";
        form.reset();
    }
    modal.show();
}
</script>
@endsection

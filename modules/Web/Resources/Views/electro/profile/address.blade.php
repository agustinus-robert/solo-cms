@extends('web::electro.index')

@section('title', "Daftar Alamat Pengiriman")

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            @include('web::electro.global.sidebar-area')
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 text-primary">
                    <h5 class="fw-bold mb-0">Daftar Alamat Pengiriman</h5>
                    <button class="btn btn-primary shadow-sm" onclick="openUpsertModal()">+ Tambah Alamat</button>
                </div>

                <div class="row">
                    @forelse($addresses as $addr)
                    <div class="col-md-6 mb-3">
                        <div class="card {{ $addr->is_main ? 'border-primary bg-light' : 'border' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="badge {{ $addr->is_main ? 'bg-primary' : 'bg-secondary' }} mb-2">{{ $addr->label }}</span>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-dark" onclick='openUpsertModal(@json($addr))'>Edit</button>
                                        @if(!$addr->is_main)
                                        <form action="{{ route('web::area.address.destroy', $addr->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus alamat ini?')">Hapus</button>
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
                    @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Belum ada alamat tersimpan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="upsertModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="upsertForm" method="POST" class="modal-content">
            @csrf
            <div id="method_field"></div> <div class="modal-header">
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

                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <label>Daerah Pengiriman</label>
                    </div>

                    <div class="col-4">
                        <select name="province_id" id="in_province" class="form-select form-select-sm" required onchange="filterCities()">
                            <option value="">Provinsi</option>
                            @foreach($provinces as $prov) <option value="{{ $prov->id }}">{{ $prov->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="city_id" id="in_city" class="form-select form-select-sm" required onchange="filterDistricts()">
                            <option value="">Kota/Kab</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="district_id" id="in_district" class="form-select form-select-sm" required>
                            <option value="">Kecamatan</option>
                        </select>
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
const allCities = @json($cities ?? []);
const allDistricts = @json($districts ?? []);

function filterCities(selectedCityId = null) {
    const provinceId = document.getElementById('in_province').value;
    const citySelect = document.getElementById('in_city');
    const districtSelect = document.getElementById('in_district');

    citySelect.innerHTML = '<option value="">Kota/Kab</option>';
    districtSelect.innerHTML = '<option value="">Kecamatan</option>';

    if (provinceId) {
        const filtered = allCities.filter(c => c.province_id == provinceId);
        filtered.forEach(city => {
            const opt = document.createElement('option');
            opt.value = city.id;
            opt.text = city.name;
            if (selectedCityId && city.id == selectedCityId) opt.selected = true;
            citySelect.add(opt);
        });
    }
}

function filterDistricts(selectedDistrictId = null) {
    const cityId = document.getElementById('in_city').value;
    const districtSelect = document.getElementById('in_district');

    districtSelect.innerHTML = '<option value="">Kecamatan</option>';

    if (cityId) {
        const filtered = allDistricts.filter(d => d.city_id == cityId);
        filtered.forEach(dist => {
            const opt = document.createElement('option');
            opt.value = dist.id;
            opt.text = dist.name;
            if (selectedDistrictId && dist.id == selectedDistrictId) opt.selected = true;
            districtSelect.add(opt);
        });
    }
}

function openUpsertModal(data = null) {
    const form = document.getElementById('upsertForm');
    const methodField = document.getElementById('method_field');
    const modalElement = document.getElementById('upsertModal');
    let modal = bootstrap.Modal.getInstance(modalElement);
    if (!modal) modal = new bootstrap.Modal(modalElement);

    if (data) {
        document.getElementById('modalTitle').innerText = 'Edit Alamat';
        let urlUpdate = "{{ route('web::area.address.update', 'ID_TEMP') }}";
        form.action = urlUpdate.replace('ID_TEMP', data.id);
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('in_label').value = data.label || '';
        document.getElementById('in_receiver').value = data.receiver_name || '';
        document.getElementById('in_phone').value = data.phone || '';
        document.getElementById('in_address').value = data.address || '';
        document.getElementById('in_rt').value = data.rt || '';
        document.getElementById('in_rw').value = data.rw || '';
        document.getElementById('in_village').value = data.village || '';
        document.getElementById('in_main').checked = !!data.is_main;

        document.getElementById('in_province').value = data.province_id || '';
        filterCities(data.city_id);
        filterDistricts(data.district_id);

    } else {
        document.getElementById('modalTitle').innerText = 'Tambah Alamat';
        form.action = "{{ route('web::area.address.store') }}";
        methodField.innerHTML = '';
        form.reset();
        document.getElementById('in_city').innerHTML = '<option value="">Kota/Kab</option>';
        document.getElementById('in_district').innerHTML = '<option value="">Kecamatan</option>';
    }

    modal.show();
}
</script>
@endsection

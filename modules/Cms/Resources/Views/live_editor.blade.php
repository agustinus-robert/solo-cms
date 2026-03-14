@extends('cms::layouts.default')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Set Permission Access</h5>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('cms::live-editor-access.store') }}">
                @csrf

                <div id="permission-wrapper">
                    @php 
                        $existingData = $currentPermissions ?? [null]; 
                    @endphp

                    @foreach($existingData as $index => $item)
                    <div class="row mb-3 permission-row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">User</label>
                            <select name="user_id[]" class="form-select" required>
                                <option value="" selected disabled>-- pilih user --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (isset($item) && $item->user_id == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status[]" class="form-select" required>
                                <option value="1" {{ (isset($item) && $item->status == 1) ? 'selected' : '' }}>Enable</option>
                                <option value="0" {{ (isset($item) && $item->status == 0) ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>

                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-outline-danger remove-row w-100">
                                Hapus
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <button type="button" id="btn-tambah-baris" class="btn btn-outline-secondary">
                        + Tambah Baris User
                    </button>

                    <button type="submit" class="btn btn-primary px-5">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Tombol hapus baris pertama selalu sembunyi */
    #permission-wrapper .permission-row:first-child .remove-row {
        display: none !important;
    }
</style>

<script>
    (function() {
        window.addEventListener('load', function() {
            
            const wrapper = document.getElementById('permission-wrapper');
            const btnAdd = document.getElementById('btn-tambah-baris');

            btnAdd.addEventListener('click', function(e) {
                e.preventDefault();
                
                const rows = document.querySelectorAll('.permission-row');
                const template = rows[0];
                const clone = template.cloneNode(true);

                const selects = clone.querySelectorAll('select');
                selects.forEach(function(select, index) {
                    if(index === 0) { // Select User
                        select.value = ""; 
                    } else { // Select Status
                        select.value = "1"; // Default Enable
                    }
                    
                    const options = select.querySelectorAll('option');
                    options.forEach(opt => opt.removeAttribute('selected'));
                });

                wrapper.appendChild(clone);
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    const currentRows = document.querySelectorAll('.permission-row');
                    if (currentRows.length > 1) {
                        e.target.closest('.permission-row').remove();
                    }
                }
            });
        });
    })();
</script>
@endsection
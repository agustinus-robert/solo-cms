@extends('account::layouts.default')

@section('title', ($role->exists ? 'Edit' : 'Tambah') . ' Role | ')

@section('extra_css')
<style>
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
    thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #343a40 !important;
    }

    tbody tr:hover {
        background-color: rgba(0,123,255,0.05) !important;
    }

    .form-check-input {
        cursor: pointer;
        transform: scale(1.2);
    }
    .menu-name {
        color: #495057;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('core::manage-role.index') }}">Role</a></li>
                    <li class="breadcrumb-item active">{{ $role->exists ? 'Edit' : 'Tambah' }}</li>
                </ol>
            </nav>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-primary font-weight-bold">
                        <i class="fas fa-user-shield mr-2"></i>
                        {{ $role->exists ? 'Konfigurasi Role: ' . $role->name : 'Buat Role Baru' }}
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('core::manage-role.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role->id }}">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Nama Role <span class="text-danger">*</span></label>
                                <input type="text" name="role_name"
                                       class="form-control @error('role_name') is-invalid @enderror"
                                       value="{{ old('role_name', $role->name) }}"
                                       placeholder="Misal: Admin Penjualan, Staff Gudang..." required>
                                @error('role_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <h6 class="font-weight-bold mb-0 text-secondary">
                                <i class="fas fa-list-check mr-1"></i> Atur Hak Akses Menu
                            </h6>
                            <small class="text-muted italic">* Centang kolom pertama untuk pilih semua aksi di baris tersebut</small>
                        </div>

                        <div class="table-responsive border rounded">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-dark text-white text-center">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%" class="text-left">Menu Utama</th>
                                        <th width="15%">View</th>
                                        <th width="15%">Create</th>
                                        <th width="15%">Edit</th>
                                        <th width="15%">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menus as $groupName => $items)
                                    <tr class="bg-light">
                                        <td colspan="6" class="py-2 px-3">
                                            <h6 class="mb-0 text-dark font-weight-bold"><i class="fas fa-folder-open mr-2 text-warning"></i> {{ $groupName }}</h6>
                                        </td>
                                    </tr>

                                    @foreach($items as $menuKey => $info)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" class="select-all-row" title="Pilih Semua Aksi">
                                        </td>
                                        <td class="pl-3 align-middle">
                                            <div class="d-flex flex-column">
                                                <span class="menu-name font-weight-bold text-primary">{{ $info['label'] }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem; line-height: 1.2;">{{ $info['desc'] }}</small>
                                            </div>
                                        </td>

                                        @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                            @php $pName = $action . '_' . $menuKey; @endphp
                                            <td class="text-center align-middle">
                                                <div class="form-check m-0 p-0 d-flex justify-content-center">
                                                    <input type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $pName }}"
                                                        class="form-check-input row-checkbox"
                                                        {{ ($role->exists && $role->hasPermissionTo($pName)) ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <a href="{{ route('core::manage-role.index') }}" class="btn btn-light px-4 mr-2 text-secondary">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Hak Akses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllRows = document.querySelectorAll('.select-all-row');

        selectAllRows.forEach(headerCheckbox => {
            headerCheckbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const rowCheckboxes = row.querySelectorAll('.row-checkbox');

                rowCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        });
    });
</script>

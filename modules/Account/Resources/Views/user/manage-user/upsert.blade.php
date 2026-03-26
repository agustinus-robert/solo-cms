@extends('account::layouts.default')

@section('title', ($user->exists ? 'Edit' : 'Tambah') . ' User | ')

@section('extra_css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da !important;
        min-height: 38px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="font-weight-bold">{{ $user->exists ? 'Update Data Pengguna' : 'Tambah Pengguna Baru' }}</h4>
        <p class="text-muted">Isi formulir di bawah ini untuk mengatur akun dan hak akses.</p>
    </div>

    <form action="{{ route('account::manage-user.store') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-id-card mr-2 text-primary"></i> Profil Pengguna
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-uppercase">
                                <i class="fas fa-user mr-2"></i> Nama Lengkap
                            </label>
                            <input type="text" name="name" class="form-control form-control-lg shadow-none"
                                   placeholder="Masukkan nama lengkap"
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-uppercase">
                                <i class="fas fa-envelope mr-2"></i> Alamat Email
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg shadow-none"
                                   placeholder="nama@email.com"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-uppercase">
                                <i class="fas fa-lock mr-2"></i> Password
                            </label>
                            <input type="password" name="password" class="form-control form-control-lg shadow-none"
                                   placeholder="{{ $user->exists ? 'Kosongkan jika tidak ganti' : 'Minimal 6 karakter' }}">
                            @if($user->exists)
                                <small class="text-info mt-2 d-block font-italic">
                                    * Biarkan kosong jika tidak ingin ganti password.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 text-dark">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-user-shield mr-2 text-success"></i> Hak Akses (Role)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-uppercase">Pilih Role</label>
                            <select name="roles[]" class="form-control select2-role shadow-none" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ in_array($role->name, $userRoles) ? 'selected' : '' }}>
                                        {{ strtoupper($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="alert alert-warning border-0 small d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle mr-3 p-2"></i>
                            <span>Pastikan pemilihan role sudah sesuai dengan wewenang pengguna.</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center mb-5" style="gap: 15px;">
                    <a href="{{ route('account::manage-user.index') }}" class="btn btn-secondary px-4 py-2">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-2"></i> Simpan User
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-role').select2({
            width: '100%',
            placeholder: " Pilih satu atau beberapa role"
        });
    });
</script>

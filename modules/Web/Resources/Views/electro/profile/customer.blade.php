@extends('web::electro.index')

@section('title', "Customer Profile Area")

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            @include('web::electro.global.sidebar-area')
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="card-header bg-white border-0 p-0 mb-4">
                    <h5 class="fw-bold mb-0">Pengaturan Profil</h5>
                    <p class="text-muted small">Kelola informasi profil dan identitas Anda.</p>
                </div>

                <form action="{{ route('web::area.customer.update', ['customer' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $user->profile && $user->profile->avatar ? asset('uploads/'.$user->profile->avatar) : asset('img/default-avatar.png') }}"
                                 class="rounded-circle border" style="width:120px; height:120px; object-fit: cover;">
                        </div>
                        <div class="mt-2">
                            <label class="small fw-bold d-block mb-1">Foto Profil</label>
                            <input type="file" name="avatar" class="form-control form-control-sm mx-auto" style="max-width: 250px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Nomor WhatsApp</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->profile->phone ?? '' }}" placeholder="081234567xxx">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">Tempat Lahir</label>
                            <input type="text" name="pob" class="form-control" value="{{ $user->profile->pob ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="dob" class="form-control" value="{{ $user->profile->dob ?? '' }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">Simpan Perubahan Profil</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

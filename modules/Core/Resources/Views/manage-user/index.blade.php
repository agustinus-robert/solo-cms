@extends('core::layouts.default')

@section('title', 'Manajemen User | ')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 font-weight-bold">Pengguna Sistem</h4>
            <p class="text-muted small mb-0">Kelola akun dan penempatan role user.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="pl-4" width="5%">NO</th>
                            <th>USER</th>
                            <th>ROLE / AKSES</th>
                            <th class="text-center">STATUS</th>
                            <th width="15%" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                        <tr>
                            <td class="pl-4 text-muted">{{ $users->firstItem() + $key }}</td>
                            <td class="py-3 px-4 align-middle">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                        class="rounded-circle shadow-sm"
                                        style="margin-right: 20px;"
                                        width="45"
                                        height="45">

                                    <div>
                                        <span class="font-weight-bold d-block text-dark mb-2" style="line-height: 1; font-size: 15px; letter-spacing: 0.5px;">
                                            {{ strtoupper($user->name) }}
                                        </span>

                                        <small class="text-muted d-flex align-items-center" style="font-size: 12px;">
                                            <i class="fas fa-envelope mr-2 text-primary" style="width: 15px;"></i>
                                            {{ $user->email }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @forelse($user->getRoleNames() as $role)
                                    <span class="badge badge-soft-primary px-2 py-1 mr-1">
                                        <i class="fas fa-shield-alt mr-1 small"></i> {{ strtoupper($role) }}
                                    </span>
                                @empty
                                    <span class="text-muted small italic">No Role</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success badge-pill" style="font-size: 10px;">ACTIVE</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                                    @if(auth()->id() !== $user->id && !session()->has('impersonate_admin_id'))
                                        <a href="{{ route('core::manage-user.impersonate', $user->id) }}"
                                           class="btn btn-info btn-sm text-white shadow-sm"
                                           title="Login Sebagai User Ini"
                                           onclick="return confirm('Login sebagai {{ $user->name }}?')">
                                            <i class="fas fa-user-secret"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('core::manage-user.edit', $user->id) }}"
                                       class="btn btn-warning btn-sm text-white shadow-sm" title="Edit User & Role">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <form action="{{ route('core::manage-user.destroy', $user->id) }}"
                                          method="POST" class="m-0 p-0"
                                          onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5">Data user tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: {{ $users->total() }} User</small>
            <div>{{ $users->links() }}</div>
        </div>
    </div>
</div>

<style>
    .badge-soft-primary { background-color: rgba(0, 123, 255, 0.1); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.2); }
</style>
@endsection

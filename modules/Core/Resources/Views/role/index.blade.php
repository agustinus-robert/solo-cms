@extends('core::layouts.default')

@section('title', 'Manajemen Role | ')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 font-weight-bold">Manajemen Role</h4>
        <a href="{{ route('core::manage-role.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Role
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="px-4" width="5%">No</th>
                            <th>Nama Role</th>
                            <th class="text-center">Permissions</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $key => $role)
                        <tr>
                            <td class="px-4 text-muted">{{ $roles->firstItem() + $key }}</td>
                            <td>
                                <strong class="text-dark">{{ strtoupper($role->name) }}</strong>
                                <br><small class="text-muted">Guard: {{ $role->guard_name }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info px-2 py-1">
                                    {{ $role->permissions->count() }} Akses
                                </span>
                            </td>
                           <td class="text-center align-middle py-3">
                                <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">

                                    <a href="{{ route('core::manage-role.edit', $role->id) }}"
                                    class="btn btn-warning btn-sm text-white shadow-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Edit
                                    </a>

                                    <form action="{{ route('core::manage-role.destroy', $role->id) }}"
                                        method="POST"
                                        class="m-0 p-0"
                                        onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Data role kosong.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($roles->hasPages())
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: {{ $roles->total() }} Data</small>
            <div>{{ $roles->links() }}</div>
        </div>
        @endif
    </div>
</div>
@endsection

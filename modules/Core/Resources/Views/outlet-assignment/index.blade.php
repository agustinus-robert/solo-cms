@extends('account::layouts.default')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-0">Manajemen Petugas per Outlet</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4">Outlet</th>
                        <th>Kode</th>
                        <th>Jumlah Petugas</th>
                        <th class="text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outlets as $outlet)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $outlet->name }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-size-11 fw-medium">
                                {{ $outlet->code }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary badge-pill font-size-12">
                                {{ $outlet->users_count ?? 0 }} Orang
                            </span>
                        </td>
                        <td class="text-end px-4">
                            <a href="{{ route('core::manage-outlet.edit', $outlet->id) }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="mdi mdi-account-cog me-1"></i> Kelola Petugas
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('account::layouts.default')

@section('title', 'Role Permission | ')

@section('content')
<form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')

    <table class="table">
        <thead>
            <tr>
                <th>Fitur / Menu</th>
                <th>Hak Akses (Centang)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
            <tr>
                <td>{{ strtoupper($permission->name) }}</td>
                <td>
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}"
                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
</form>
@endsection

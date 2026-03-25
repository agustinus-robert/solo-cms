<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    // Menampilkan halaman centangan
    public function edit(Role $role)
    {
        $permissions = Permission::all(); // Ambil semua daftar centangan
        $rolePermissions = $role->permissions->pluck('id')->toArray(); // Centangan yang sudah ada

        return view('account::rolepermission.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    // Menyimpan hasil centangan
    public function update(Request $request, Role $role)
    {
        // $request->permissions berisi array ID dari checkbox yang dicentang
        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Hak akses berhasil diperbarui!');
    }
}

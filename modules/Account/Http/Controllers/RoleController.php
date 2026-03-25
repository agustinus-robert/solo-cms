<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected $menus = [
        'outlet', 'product', 'adjustment', 'supplier', 'report', 'pos', 'brand',
        'taxrate', 'tier', 'unit', 'category', 'reporting', 'promotion',
        'purchase', 'quotation', 'return', 'sale', 'transfer'
    ];

    // 1. Tampilan Utama
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->latest()
            ->paginate(10);

        return view('account::role.index', compact('roles'));
    }

    // 2. Tampilan Tambah (Lari ke Upsert)
    public function create()
    {
        $role = new Role(); // Data kosong
        $menus = $this->menus;
        return view('account::role.upsert', compact('role', 'menus'));
    }

    // 3. Tampilan Edit (Lari ke Upsert juga)
    public function edit($id)
    {
        $role = Role::where('id', $id)->firstOrFail();
        $menus = $this->menus;
        return view('account::role.upsert', compact('role', 'menus'));
    }

    // 4. Proses Simpan (Store/Update)
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::updateOrCreate(
            ['id' => $request->role_id],
            ['name' => $request->role_name, 'guard_name' => 'web']
        );

        $role->syncPermissions($request->permissions ?? []);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('account::manage-role.index')->with('success', 'Berhasil simpan, Cok!');
    }

    // 5. Proses Hapus
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->back()->with('success', 'Role dihapus!');
    }
}

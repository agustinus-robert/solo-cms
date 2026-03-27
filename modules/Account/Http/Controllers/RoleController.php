<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected $menus = [
        'POS (Point of Sale)' => [
            'outlet'     => ['label' => 'Outlet', 'desc' => 'Mengelola cabang atau lokasi toko.'],
            'product'    => ['label' => 'Produk', 'desc' => 'Daftar barang, harga, dan stok.'],
            'adjustment' => ['label' => 'Stok Opname', 'desc' => 'Penyesuaian jumlah stok manual.'],
            'supplier'   => ['label' => 'Supplier', 'desc' => 'Daftar pemasok barang.'],
            'pos'        => ['label' => 'Transaksi Kasir', 'desc' => 'Akses ke menu penjualan/kasir.'],
            'brand'      => ['label' => 'Brand', 'desc' => 'Manajemen merk produk.'],
            'unit'       => ['label' => 'Satuan', 'desc' => 'Satuan barang (Pcs, Box, Kg).'],
            'category'   => ['label' => 'Kategori', 'desc' => 'Pengelompokan jenis produk.'],
            'warehouse'  => ['label' => 'Gudang', 'desc' => 'Lokasi penyimpanan fisik barang.'],
            'transfer'   => ['label' => 'Transfer Stok', 'desc' => 'Mutasi barang antar outlet/gudang.'],
        ],
        'Account & Security' => [
            'role' => ['label' => 'Role Permission', 'desc' => 'Mengatur hak akses kelompok user.'],
            'user' => ['label' => 'Manajemen User', 'desc' => 'Menambah atau memblokir staf.'],
        ],
        'Content Management (CMS)' => [
            'posting' => ['label' => 'Artikel/Post', 'desc' => 'Membuat konten berita atau artikel.'],
            'menu'    => ['label' => 'Navigasi Menu', 'desc' => 'Mengatur struktur menu website.'],
            'order'   => ['label' => 'Pesanan Online', 'desc' => 'Mengatur urutan menu.'],
        ]
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

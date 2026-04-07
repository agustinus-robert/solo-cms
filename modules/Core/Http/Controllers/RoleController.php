<?php

namespace Modules\Core\Http\Controllers;

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
            'taxrate'    => ['label' => 'Pajak', 'desc' => 'Pengaturan persentase pajak transaksi.'],
            'tier'       => ['label' => 'Tier Harga', 'desc' => 'Leveling harga untuk pelanggan tertentu.'],
            'promotion'  => ['label' => 'Promosi', 'desc' => 'Mengatur diskon atau campaign penjualan.'],
            'purchase'   => ['label' => 'Pembelian', 'desc' => 'Mencatat transaksi beli ke supplier.'],
            'quotation'  => ['label' => 'Penawaran', 'desc' => 'Membuat surat penawaran harga (Quotation).'],
            'return'     => ['label' => 'Retur', 'desc' => 'Pengembalian barang dari pelanggan atau ke supplier.'],
            'sale'       => ['label' => 'Penjualan', 'desc' => 'Rekap data transaksi penjualan.'],
            'casier'     => ['label' => 'Data Kasir', 'desc' => 'Manajemen sesi dan petugas kasir.'],
        ],
        'Human Resource (HRM)' => [
            'department'        => ['label' => 'Departemen', 'desc' => 'Manajemen divisi atau departemen kerja.'],
            'position'          => ['label' => 'Jabatan', 'desc' => 'Mengatur level jabatan karyawan.'],
            'employee'          => ['label' => 'Data Karyawan', 'desc' => 'Master data informasi staf.'],
            'employee_schedule' => ['label' => 'Jadwal Kerja', 'desc' => 'Pengaturan shift dan alokasi jadwal.'],
            'employee_scanlog'  => ['label' => 'Log Absensi', 'desc' => 'Riwayat scan masuk/pulang karyawan.'],
            'leave_category'    => ['label' => 'Kategori Izin', 'desc' => 'Jenis-jenis izin tidak masuk kerja.'],
            'employee_leave'    => ['label' => 'Pengajuan Izin', 'desc' => 'Manajemen izin/sakit karyawan.'],
            'vacation_category' => ['label' => 'Kategori Cuti', 'desc' => 'Pengaturan jenis cuti tahunan/khusus.'],
            'vacation_quota'    => ['label' => 'Kuota Cuti', 'desc' => 'Manajemen jatah cuti per karyawan.'],
            'employee_vacation' => ['label' => 'Pengajuan Cuti', 'desc' => 'Proses persetujuan cuti karyawan.'],
            'outwork_category'  => ['label' => 'Kategori Dinas', 'desc' => 'Jenis tugas luar kantor.'],
            'employee_outwork'  => ['label' => 'Tugas Dinas', 'desc' => 'Pencatatan karyawan yang bertugas di luar.'],
            'employee_overtime' => ['label' => 'Lembur', 'desc' => 'Pengajuan dan rekap lembur karyawan.'],
            'employee_loan'     => ['label' => 'Pinjaman/Kasbon', 'desc' => 'Manajemen pinjaman staf.'],
            'ticketing'         => ['label' => 'Helpdesk/Tiket', 'desc' => 'Sistem bantuan internal karyawan.'],
        ],
        'Payroll, Tax & Insurance' => [
            'slip'                         => ['label' => 'Slip Gaji', 'desc' => 'Generate dan cetak slip gaji.'],
            'slip_category'                => ['label' => 'Kategori Slip', 'desc' => 'Pengelompokan tipe slip gaji.'],
            'slip_component'               => ['label' => 'Komponen Gaji', 'desc' => 'Master tunjangan dan potongan.'],
            'slip_template'                => ['label' => 'Template Slip', 'desc' => 'Format tampilan slip gaji.'],
            'employee_payroll_template'    => ['label' => 'Template Payroll', 'desc' => 'Pengaturan payroll per individu.'],
            'employee_payroll_calculation' => ['label' => 'Hitung Payroll', 'desc' => 'Proses kalkulasi gaji bulanan.'],
            'salaray_approval'             => ['label' => 'Persetujuan Gaji', 'desc' => 'Verifikasi pembayaran gaji.'],
            'employee_validations_salary'  => ['label' => 'Validasi Gaji', 'desc' => 'Pengecekan akhir nominal gaji.'],
            'employee_feastday'            => ['label' => 'THR', 'desc' => 'Manajemen tunjangan hari raya.'],
            'employee_postyear'            => ['label' => 'Bonus Tahunan', 'desc' => 'Manajemen bonus akhir tahun.'],
            'isurance_registration'        => ['label' => 'Registrasi Asuransi', 'desc' => 'Pendaftaran asuransi karyawan.'],
            'isurance_template_bpjs'       => ['label' => 'Template BPJS', 'desc' => 'Konfigurasi perhitungan BPJS.'],
            'employee_isurance'            => ['label' => 'Asuransi Karyawan', 'desc' => 'Data asuransi aktif per staf.'],
            'employee_tax'                 => ['label' => 'Pajak Karyawan', 'desc' => 'Rekapitulasi pajak penghasilan.'],
            'employee_ter_taxs'            => ['label' => 'TER Pajak', 'desc' => 'Perhitungan Tarif Efektif Rata-rata.'],
            'employee_income_yearly'       => ['label' => 'SPT Tahunan', 'desc' => 'Rekap pendapatan tahunan karyawan.'],
        ],
        'Account & Security' => [
            'role'        => ['label' => 'Role Permission', 'desc' => 'Mengatur hak akses kelompok user.'],
            'user'        => ['label' => 'Manajemen User', 'desc' => 'Menambah atau memblokir staf.'],
            'validations' => ['label' => 'Validasi Data', 'desc' => 'Sistem verifikasi keamanan data.'],
        ],
        'Content Management (CMS)' => [
            'posting'      => ['label' => 'Artikel/Post', 'desc' => 'Membuat konten berita atau artikel.'],
            'menu'         => ['label' => 'Navigasi Menu', 'desc' => 'Mengatur struktur menu website.'],
            'order'        => ['label' => 'Pesanan Online', 'desc' => 'Mengatur urutan menu.'],
            'postImage'    => ['label' => 'Galeri Foto', 'desc' => 'Manajemen aset gambar postingan.'],
            'postVideo'    => ['label' => 'Galeri Video', 'desc' => 'Manajemen aset video postingan.'],
            'category'     => ['label' => 'Kategori Konten', 'desc' => 'Pengelompokan artikel.'],
            'categoryName' => ['label' => 'Label Kategori', 'desc' => 'Manajemen nama label kategori.'],
            'tags'         => ['label' => 'Tags', 'desc' => 'Label kata kunci konten.'],
            'custom'       => ['label' => 'Halaman Kustom', 'desc' => 'Membuat landing page statis.'],
        ],
        'Reporting' => [
            'report'                  => ['label' => 'Laporan Ringkas', 'desc' => 'Akses cepat ke ringkasan data.'],
            'reporting'               => ['label' => 'Pusat Laporan', 'desc' => 'Kumpulan laporan mendalam.'],
            'employee_report'         => ['label' => 'Laporan SDM', 'desc' => 'Statistik dan data karyawan.'],
            'employee_report_salary'  => ['label' => 'Laporan Gaji', 'desc' => 'Rekapitulasi pengeluaran gaji.'],
            'employee_report_overtime' => ['label' => 'Laporan Lembur', 'desc' => 'Analitik lembur karyawan.'],
        ],
    ];

    // 1. Tampilan Utama
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->latest()
            ->paginate(10);

        return view('core::role.index', compact('roles'));
    }

    public function create()
    {
        $role = new Role(); // Data kosong
        $menus = $this->menus;
        return view('core::role.upsert', compact('role', 'menus'));
    }

    public function edit($id)
    {
        $role = Role::where('id', $id)->firstOrFail();
        $menus = $this->menus;
        return view('account::role.upsert', compact('role', 'menus'));
    }

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

        return redirect()->route('core::manage-role.index')->with('success', 'Berhasil simpan!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->back()->with('success', 'Role dihapus!');
    }
}

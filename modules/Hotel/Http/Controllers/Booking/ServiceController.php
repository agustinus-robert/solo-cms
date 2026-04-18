<?php

namespace Modules\Hotel\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\AdditionalService;
use Modules\Hotel\Repositories\Booking\ServiceRepositories;

class ServiceController extends Controller
{
    use ServiceRepositories;

    /**
     * Simpan layanan tambahan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'   => 'required|exists:hotel_bookings,id',
            'service_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'quantity'     => 'required|integer|min:1',
        ]);

        try {
            $this->addServiceToBooking($request->booking_id, $request->all());

            return back()->with('success', 'Layanan tambahan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambah layanan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus layanan tambahan.
     */
    public function destroy($id)
    {
        try {
            $this->deleteService($id);

            return back()->with('success', 'Layanan berhasil dihapus dan tagihan telah diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus layanan: ' . $e->getMessage());
        }
    }
}

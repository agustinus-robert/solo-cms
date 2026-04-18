<?php

namespace Modules\Hotel\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Guests;
use Modules\Hotel\Repositories\Guests\GuestRepositories;
use Modules\Hotel\Http\Requests\Guests\StoreRequest;
use Modules\Hotel\Http\Requests\Guests\UpdateRequest;

class GuestController extends Controller
{
    use GuestRepositories;

    public function index(Request $request)
    {
        $guests = $this->getGuests($request->only('search'));

        if ($request->ajax()) {
            return view('hotel::guests._table', compact('guests'))->render();
        }

        return view('hotel::guests.index', compact('guests'));
    }

    public function create()
    {
        $guest = null;
        return view('hotel::guests.upsert', compact('guest'));
    }

   /**
     * Simpan tamu baru.
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->transform();
            $this->upsertGuest($data);

            return redirect()->route('hotel::guest.index')
                ->with('success', 'Tamu ' . $data['first_name'] . ' berhasil didaftarkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Form edit tamu.
     * Pastikan menggunakan model Guests (plural) sesuai file model kamu.
     */
    public function edit(Guests $guest)
    {
        return view('hotel::guests.upsert', compact('guest'));
    }

    /**
     * Update data tamu.
     */
    public function update(UpdateRequest $request, Guests $guest)
    {
        try {
            $data = $request->transform();
            $this->upsertGuest($data, $guest->id);

            return redirect()->route('hotel::guest.index')
                ->with('success', 'Data ' . $guest->full_name . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data tamu.
     */
    public function destroy(Guests $guest)
    {
        try {
            $this->deleteGuest($guest->id);

            $message = "Data tamu {$guest->full_name} berhasil dihapus.";

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('hotel::guest.index')->with('success', $message);

        } catch (\Exception $e) {
            $error = $e->getMessage();

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $error
                ], 422);
            }

            return back()->with('error', $error);
        }
    }
}

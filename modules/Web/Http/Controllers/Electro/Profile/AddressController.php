<?php
namespace Modules\Web\Http\Controllers\Electro\Profile;

use Modules\Web\Http\Controllers\Global\Profile\BaseAddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Models\UserAddress;

class AddressController extends BaseAddressController
{
    protected $theme = 'electro';

    public function index()
    {
        $user = Auth::user();
        $addresses = UserAddress::where('user_id', $user->id)
            ->orderBy('is_main', 'desc')
            ->get();
        $canEdit = false;

        return view("web::electro.profile.address", compact('user', 'addresses', 'canEdit'));
    }

    public function store(Request $request)
    {
        $this->saveAddress($request, null);
        return $this->handleResponse('Alamat baru berhasil disimpan!');
    }

    public function update(Request $request, $id = null)
    {
        $this->saveAddress($request, $id);
        return $this->handleResponse('Alamat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $address = UserAddress::where('user_id', Auth::id())->findOrFail($id);

        if ($address->is_main) {
            return back()->with('error', 'Alamat utama tidak bisa dihapus!');
        }

        $address->delete();
        return $this->handleResponse('Alamat berhasil dihapus!');
    }
}

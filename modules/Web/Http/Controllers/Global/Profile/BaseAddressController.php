<?php

namespace Modules\Web\Http\Controllers\Global\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Web\Traits\HasProfileLogic;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Models\UserAddress; // Import model alamat

abstract class BaseAddressController extends Controller
{
    use HasProfileLogic;

    abstract public function index();

    protected function saveAddress(Request $request, $id = null)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'receiver_name'  => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string',
            'rt'             => 'nullable|string|max:5',
            'rw'             => 'nullable|string|max:5',
            'village'        => 'nullable|string|max:100',
            'is_main'        => 'nullable|boolean',
            'province_id'    => 'required',
            'city_id'    => 'required',
            'district_id' => 'required'
        ]);

        $user = Auth::user();

        if ($request->has('is_main') && $request->is_main) {
            UserAddress::where('user_id', $user->id)->update(['is_main' => 0]);
        }

        return UserAddress::updateOrCreate(
            ['id' => $id, 'user_id' => $user->id],
            $data
        );
    }

    public function update(Request $request, $id = null)
    {
        $this->performProfileUpdate($request);
        return $this->handleResponse('Profil berhasil diperbarui.');
    }

    protected function handleResponse($message)
    {
        return back()->with('success', $message);
    }
}

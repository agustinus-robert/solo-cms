<?php

namespace Modules\Web\Http\Controllers\Global\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Web\Traits\HasProfileLogic;
use Illuminate\Support\Facades\Auth;

abstract class BaseAddressController extends Controller
{
    use HasProfileLogic;

    abstract public function index();

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

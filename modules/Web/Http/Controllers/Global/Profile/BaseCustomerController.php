<?php

namespace Modules\Web\Http\Controllers\Global\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Web\Traits\HasProfileLogic;

abstract class BaseCustomerController extends Controller
{
    use HasProfileLogic;

    public function update(Request $request)
    {
        $this->performProfileUpdate($request);

        return $this->handleResponse('Data customer berhasil diperbarui!');
    }

    protected function handleResponse($message)
    {
        return back()->with('success', $message);
    }
}

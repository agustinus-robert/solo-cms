<?php

namespace Modules\Web\Http\Controllers\Electro\Profile;

use Modules\Web\Http\Controllers\Global\Profile\BaseCustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Models\UserAddress;

class CustomerController extends BaseCustomerController
{
    protected $theme = 'electro';

    public function index()
    {
        $user = Auth::user()->load('profile', 'addresses');
        $canEdit = false;
        return view("web::electro.profile.customer", compact('user', 'canEdit'));
    }

    public function update(Request $request)
    {
        $this->performProfileUpdate($request);
        return $this->handleResponse('Data profil Robert berhasil diperbarui!');
    }
}

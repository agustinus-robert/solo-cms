<?php

namespace Modules\HRMS\Http\Controllers\System;

use Hash;
use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Account\Repositories\UserRepository;
use Modules\Core\Http\Requests\System\User\StoreRequest;
// Import Controller Utama sebagai Base
use Modules\Core\Http\Controllers\System\UserController as BaseUserController;
use Modules\Core\Models\CompanyRole;
use Modules\Account\Enums\MariageEnum;
use Modules\HRMS\Models\EmployeeDocument;

class UserController extends BaseUserController
{
    use UserRepository;

    public function index(Request $request)
    {
        $baseView = parent::index($request);
        
        $data = $baseView->getData();

        return view('hrms::users.index', $data);
    }

    public function store(StoreRequest $request)
    {
        parent::store($request);
        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }


    public function show(Request $request, User $user)
    {
        $baseView = parent::show($request, $user);
        $data = $baseView->getData();

        return view('hrms::users.show', $data);
    }

    public function destroy(User $user)
    {
        parent::destroy($user);
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function restore(User $user)
    {
        parent::restore($user);
        return redirect()->back()->with('success', 'Data berhasil dipulihkan.');
    }

    public function kill(User $user)
    {
        parent::kill($user);
        return redirect()->back()->with('success', 'Data dihapus permanen.');
    }

    public function repass(User $user)
    {
        // Kita panggil parent, tapi karena repass di bapak ada password baru di pesan success,
        // lebih aman biarkan return aslinya atau modifikasi pesan di sini.
        return parent::repass($user);
    }

    public function login(Request $request, User $user)
    {
        // Untuk login biasanya redirect ke halaman home, 
        // jadi kita ikut saja return asli dari bapaknya.
        return parent::login($request, $user);
    }
}
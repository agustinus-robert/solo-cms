<?php

namespace Modules\Core\Http\Controllers\System;

use Hash;
use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Account\Repositories\UserRepository;
use Modules\Core\Http\Requests\System\User\StoreRequest;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Models\CompanyRole;
use Modules\Account\Enums\MariageEnum;
use Modules\HRMS\Models\EmployeeDocument;

class UserController extends Controller
{
    use UserRepository;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('roles', 'meta')
            ->search($request->get('search'))
            ->whenTrashed($request->get('trash'))
            ->paginate($request->get('limit', 10));

        $users_count = User::count();

        return view('core::system.users.index', compact('users', 'users_count'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($user = $this->storeUser($request->transformed()->toArray())) {
            return redirect()->next()->with('success', 'Pengguna <strong>' . $user->name . ' (' . $user->username . ')</strong> berhasil dibuat dengan password <strong>' . $request->password . '</strong>');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user)
    {
        $roles = CompanyRole::all();
        $mariages = MariageEnum::cases();

        $documents = EmployeeDocument::with(['employee.user'])
        ->whereHas('employee', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->get();

        return in_array($request->get('page', 'profile'), ['profile', 'username', 'email', 'phone', 'role', 'tax', 'document'])
            ? view('core::system.users.show', compact('user', 'roles', 'mariages', 'documents'))
            : abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user = $this->removeUser($user)) {
            return redirect()->next()->with('success', 'Pengguna <strong>' . $user->name . ' (' . $user->username . ')</strong> berhasil dihapus');
        }
        return redirect()->fail();
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(User $user)
    {
        if ($user = $this->restoreUser($user)) {
            return redirect()->next()->with('success', 'Pengguna <strong>' . $user->name . ' (' . $user->username . ')</strong> berhasil dipulihkan');
        }
        return redirect()->fail();
    }

    /**
     * Kill the specified resource from storage.
     */
    public function kill(User $user)
    {
        if ($user = $this->killUser($user)) {
            return redirect()->next()->with('success', 'Pengguna <strong>' . $user->name . ' (' . $user->username . ')</strong> berhasil dihapus permanen dari sistem');
        }
        return redirect()->fail();
    }

    /**
     * Reset password from storage.
     */
    public function repass(User $user)
    {
        if ($password = $this->resetPasswordUser($user)) {
            return redirect()->next()->with('success', 'Sandi pengguna <strong>' . $user->name . ' (' . $user->username . ')</strong> berhasil diperbarui menjadi <strong>' . $password . '</strong>');
        }
        return redirect()->fail();
    }

    /**
     * Cross login with bypassing user password.
     */
    public function login(Request $request, User $user)
    {
        if (!Hash::check($request->input('password'), $request->user()->password))
            return redirect()->fail('Mohon maaf, sandi yang Anda masukkan salah, silakan ulangi kembali!');

        if ($user = $this->crossLoginUser($request, $user)) {
            return redirect()->route('account::home')->with('success', 'Anda telah masuk ke pengguna <strong>' . $user->name . ' (' . $user->username . ')</strong>, data Anda tetap terekam oleh sistem.');
        }

        return redirect()->fail();
    }
}

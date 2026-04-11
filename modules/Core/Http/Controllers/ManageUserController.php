<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Account\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ManageUserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('core::manage-user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'web')->get();
        return view('core::manage-user.upsert', [
            'user' => new User(),
            'roles' => $roles,
            'userRoles' => []
        ]);
    }

    public function impersonate($id)
    {
        $userToImpersonate = User::findOrFail($id);
        if ($userToImpersonate->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda sudah berada di akun ini.');
        }

        Session::put('impersonate_admin_id', auth()->id());
        Auth::login($userToImpersonate);

        return redirect()->route('portal::dashboard-msdm.index')
                ->with('success', "Sekarang Anda login sebagai {$userToImpersonate->name}");    }

    /**
     * Fitur Kembali ke Akun Administrator
     */
    public function leaveImpersonate()
    {
        $adminId = Session::get('impersonate_admin_id');

        if ($adminId) {
            $admin = User::findOrFail($adminId);
            Auth::logout();
            Auth::login($admin);
            request()->session()->regenerate();
            Session::forget('impersonate_admin_id');

            return redirect()->route('portal::dashboard-msdm.index')
                ->with('success', "Berhasil kembali! Anda sekarang login sebagai {$admin->name}");
        }

        return redirect()->route('portal::dashboard-msdm.index');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('guard_name', 'web')->get();

        $userRoles = $user->roles->pluck('name')->toArray();

        return view('core::manage-user.upsert', compact('user', 'roles', 'userRoles'));
    }

    public function store(Request $request)
    {
        $userId = $request->user_id;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . ($userId ?? 'NULL'),
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'roles'    => 'required|array'
        ]);

        $user = User::updateOrCreate(
            ['id' => $request->user_id],
            [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password ? $request->password : User::find($request->user_id)->password,
            ]
        );

        $user->syncRoles($request->roles);

        return redirect()->route('core::manage-user.index')->with('success', 'Data User & Role berhasil disimpan!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Tidak bisa hapus akun!');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus!');
    }
}

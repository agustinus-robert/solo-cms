<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Modules\Account\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('account::user.profile', compact('user'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() != $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'old_password' => 'nullable|required_with:password',
            'password'     => 'nullable|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Password lama tidak sesuai!']);
            }
            $user->password = $request->password;
        }

        $user->name     = $request->name;
        $user->username = $request->username;
        $user->email    = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}

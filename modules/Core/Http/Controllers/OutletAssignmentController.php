<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Poz\Models\Outlet;
use Modules\Account\Models\User;
use Illuminate\Support\Facades\DB;

class OutletAssignmentController extends Controller
{
    public function index()
    {
        $outlets = Outlet::withCount('users')->latest()->paginate(10);
        return view('core::outlet-assignment.index', compact('outlets'));
    }

    public function edit($id)
    {
        $outlet = Outlet::with('users')->findOrFail($id);

        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['outlet', 'casier']);
        })->get(['id', 'name', 'email']);

        return view('core::outlet-assignment.edit', compact('outlet', 'users'));
    }

    public function update(Request $request, $id)
    {
        $outlet = Outlet::findOrFail($id);

        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);


        $outlet->users()->sync($request->user_ids ?? []);

        return redirect()->route('core::manage-outlet.index')
                         ->with('success', "Petugas di outlet $outlet->name telah diperbarui.");
    }
}

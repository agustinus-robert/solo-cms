<?php

namespace Modules\Cms\Http\Controllers;

use Modules\Cms\Models\CmsLiveEditorsAccess;
use Modules\Reference\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Admin\Models\CmsPost;
use Modules\Admin\Models\CmsContact;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;

class LiveEditorAccessController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function create()
    {
        $users = User::whereNull('deleted_at')->get();
        $currentPermissions = CmsLiveEditorsAccess::all();

        return view('cms::live_editor', compact('users', 'currentPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id.*' => 'required|exists:users,id',
            'status.*'  => 'required|boolean'
        ]);

        foreach ($request->user_id as $i => $userId) {

            CmsLiveEditorsAccess::updateOrCreate(
                ['user_id' => $userId],
                ['status'  => $request->status[$i]]
            );
        }

        return back()->with('success', 'Permission Live Builder berhasil disimpan');
    }
}

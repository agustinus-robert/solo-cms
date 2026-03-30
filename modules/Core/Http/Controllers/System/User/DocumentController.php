<?php

namespace Modules\Core\Http\Controllers\System\User;

use Modules\Account\Models\User;
use Modules\Core\Http\Controllers\Controller;
use Modules\Account\Http\Requests\User\Document\StoreRequest;
use Modules\Account\Http\Requests\User\Document\UpdateRequest;
use Modules\Account\Repositories\User\DocumentRepository;

class DocumentController extends Controller
{
    use DocumentRepository;

    public function store(User $user, StoreRequest $request){

        if($empdocs = $this->storeFile($request->transform())){
            return redirect()->back()->with('success', 'Dokumen pengguna <strong>' . $empdocs->employee->user->name . '</strong> telah berhasil ditambahkan.');
        }
    }

    public function destroy($id)
    {
        if ($this->deleteFile($id)) {
            return redirect()->back()->with('success', 'Dokumen telah berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
    }
}

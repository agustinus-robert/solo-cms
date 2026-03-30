<?php

namespace Modules\HRMS\Http\Controllers\System\User;

use Modules\HRMS\Models\Employee;
use Modules\Core\Http\Controllers\Controller;
use Modules\Account\Http\Requests\User\Document\StoreRequest;
use Modules\Account\Http\Requests\User\Document\UpdateRequest;
use Modules\Account\Repositories\User\DocumentRepository;

class DocumentController extends Controller
{
    use DocumentRepository;

    public function store(StoreRequest $request){
        if($empdocs = $this->storeFile($request->transform())){
            return redirect()->back()->with('success', 'Dokumen pengguna <strong>' . $empdocs->employee->user->name . '</strong> telah berhasil ditambahkan.');
        }

        return redirect()->back()->with('error', 'Gagal menambahkan dokumen.');
    }

    public function destroy($id)
    {
        if ($this->deleteFile($id)) {
            return redirect()->back()->with('success', 'Dokumen telah berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
    }
}

<?php

namespace Modules\Portal\Http\Controllers\Files;

use Modules\HRMS\Http\Controllers\System\User\DocumentController as BaseDocumentController;
use Modules\HRMS\Models\Employee;
use Modules\Account\Http\Requests\User\Document\StoreRequest;
use Illuminate\Http\Request;
use Modules\HRMS\Models\EmployeeDocument;

class DocumentController extends BaseDocumentController
{
    /**
     * Tampilkan form create dokumen untuk user
     */
    public function index(Request $request)
    {
        $employee = request()->user()->employee;

        $documents = EmployeeDocument::where('empl_id', $employee->id)->get();
        return view('portal::documents.index', compact('documents', 'employee'));
    }

    /**
     * Simpan dokumen baru untuk user
     */
    public function store(StoreRequest $request)
    {
        return parent::store($request);
    }

    /**
     * Hapus dokumen user
     */
    public function destroy($id)
    {
        return parent::destroy($id);
    }
}

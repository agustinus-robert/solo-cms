<?php

namespace Modules\HRMS\Http\Controllers\Employment;

use Modules\HRMS\Models\Employee;
use Illuminate\Http\Request;
use Modules\Account\Models\User;
use Modules\Core\Http\Controllers\Controller;
use Modules\Account\Repositories\User\TaxRepository;
use Modules\Core\Http\Requests\System\User\Role\UpdateRequest;

class TaxController extends Controller
{
    use TaxRepository;
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Employee $employee, Request $request)
    {
        $data = [
            'tax_status' => $request->is_tax_active
        ];

        if($this->updateTax($employee, $data)){
            return redirect()->back()->with('success', 'Pajak pengguna <strong>' . $employee->user->name . '</strong> telah berhasil diperbarui.');
        }

        return redirect()->fail();
    }
}

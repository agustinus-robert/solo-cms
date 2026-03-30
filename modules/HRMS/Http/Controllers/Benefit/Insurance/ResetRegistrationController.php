<?php

namespace Modules\HRMS\Http\Controllers\Benefit\Insurance;

use Illuminate\Http\Request;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeInsurance;
use Modules\HRMS\Repositories\EmployeeInsuranceRepository;
use Modules\HRMS\Http\Requests\Benefit\Insurance\StoreRequest;
use Modules\HRMS\Http\Controllers\Controller;

class ResetRegistrationController extends Controller
{
    use EmployeeInsuranceRepository;

    private $insuranceHealthId = 1;
    private $insuranceEmployeeId = 2;

    /**
     * Display all resource
     * */
    public function resetHealthInsurance(Employee $employee, Request $request)
    {
        if ($this->doResetInsurance($employee, $this->getPriceInsurance($this->insuranceHealthId))) {
            return redirect()->next()->with('success', 'Tarif asuransi kesehatan karyawan atas nama <strong>' . $employee->user->name . '</strong> telah berhasil dihapus.');
        }
        return redirect()->fail();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function resetEmployeeInsurance(Employee $employee, Request $request)
    {
        if ($this->doResetInsurance($employee, $this->getPriceInsurance($this->insuranceEmployeeId))) {
            return redirect()->next()->with('success', 'Tarif asuransi ketenagakerjaan karyawan atas nama <strong>' . $employee->user->name . '</strong> telah berhasil dihapus.');
        }
        return redirect()->fail();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function batchResetHealthInsurance(StoreRequest $request)
    {
        $employees = Employee::has('contract')->get();

        if ($this->batchResetInsurance($employees, $this->getPriceInsurance($this->insuranceHealthId))) {
            $errors = $this->getBatchErrors();
            if ($errors->isNotEmpty()) {
                return redirect()->next()->with(
                    'success',
                    'Berhasil menghapus tarif asuransi kesehatan, namun <strong>' .
                        $errors->count() .
                        '</strong> karyawan gagal diproses.'
                )->with('failed', $errors);
            }

            return redirect()->next()->with('success', 'Berhasil menghapus tarif asuransi kesehatan untuk <strong>' . count($employees) . '</strong> karyawan.');
        }
        return redirect()->fail();
    }

    public function batchResetEmployeeInsurance(EmployeeInsurance $insurance)
    {
        $employees = Employee::has('contract')->get();

        if ($this->batchResetInsurance($employees, $this->getPriceInsurance($this->insuranceEmployeeId))) {
            $errors = $this->getBatchErrors();
            if ($errors->isNotEmpty()) {
                return redirect()->next()->with(
                    'success',
                    'Berhasil menghapus tarif asuransi kesehatan, namun <strong>' .
                        $errors->count() .
                        '</strong> karyawan gagal diproses.'
                )->with('failed', $errors);
            }
            return redirect()->next()->with('success', 'Berhasil menghapus tarif asuransi ketenagakerjaan untuk <strong>' . count($employees) . '</strong> karyawan.');
        }
        return redirect()->fail();
    }
}

<?php

namespace Modules\HRMS\Http\Controllers\Employment;

use Illuminate\Http\Request;
use Modules\Core\Models\CompanyContract;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Repositories\EmployeeRepository;
use Modules\HRMS\Http\Requests\Employment\Employee\StoreRequest;
use Modules\HRMS\Http\Requests\Employment\Employee\UpdateRequest;
use Modules\HRMS\Http\Controllers\Controller;
use Modules\HRMS\Models\EmployeeDocument;

class EmployeeController extends Controller
{
    use EmployeeRepository;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = Employee::with('user.meta', 'contract.positions',
                'contract.positions.position')
            ->search($request->get('search'))
            ->whenTrashed($request->get('trash'))
            ->paginate($request->get('limit', 10));

        $employees_count = Employee::count();



        return view('hrms::employment.employees.index', compact('employees', 'employees_count'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = CompanyContract::all();
        return view('hrms::employment.employees.create', compact('contracts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($employee = $this->storeEmployee($request->transformed()->toArray())) {
            return redirect()->next()->with('success', 'Karyawan baru dengan nama <strong>' . $employee->user->name . ' (' . $employee->user->username . ')</strong> berhasil dibuat dengan sandi <strong>' . $request->password . '</strong>');
        }
        return redirect()->fail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Employee $employee, UpdateRequest $request)
    {
        if ($employee = $this->updateEmployee($employee, $request->transformed()->toArray())) {
            return redirect()->next()->with('success', 'Informasi kepegawaian <strong>' . $employee->user->name . ' (' . $employee->user->username . ')</strong> telah berhasil diperbarui.');
        }
        return redirect()->fail();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Employee $employee)
    {
        $employee = $employee->load('user.meta', 'contracts', 'positions');

        $documents = EmployeeDocument::where('empl_id', $employee->id)
        ->whereNull('deleted_at')
        ->get();

        return view('hrms::employment.employees.show', compact('employee', 'documents'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee = $this->destroyEmployee($employee)) {
            return redirect()->next()->with('success', 'Karyawan <strong>' . $employee->user->name . ' (' . $employee->user->username . ')</strong> berhasil dihapus');
        }
        return redirect()->fail();
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(Employee $employee)
    {
        if ($employee = $this->restoreEmployee($employee)) {
            return redirect()->next()->with('success', 'Karyawan <strong>' . $employee->user->name . ' (' . $employee->user->username . ')</strong> berhasil dipulihkan');
        }
        return redirect()->fail();
    }
}

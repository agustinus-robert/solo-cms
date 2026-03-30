<?php

namespace Modules\Portal\Http\Controllers\Loan;

use Illuminate\Http\Request;
use Modules\HRMS\Models\EmployeeLoan;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Models\CompanyApprovable;
use Modules\HRMS\Models\EmployeePosition;
use Modules\Portal\Http\Controllers\Controller;
use Modules\Portal\Http\Requests\Loan\Manage\UpdateRequest;
use Modules\Portal\Notifications\Loan\Manage\ApprovedNotification;
use Modules\Portal\Notifications\Loan\Manage\FinanceNotification;
use Modules\Portal\Notifications\Loan\Manage\RejectedNotification;
use Modules\Portal\Notifications\Loan\Submission\SubmissionNotification;

class ManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee->load('position.position.children');

        $loans = EmployeeLoan::with('approvables.userable.position', 'employee.user', 'childrens.parent', 'category')
            ->whereNull('parent_id')
            ->whereHas('approvables', fn($approvable) => $approvable->where('userable_id', $employee->position->id))
            ->whenOnlyPending($request->get('pending'))
            ->search($request->get('search'))
            ->latest()
            ->paginate($request->get('limit', 10));

        $pending_loans_count = EmployeeLoan::whereHas('employee.position', fn($position) => $position->whereIn('position_id', $employee->position->position->children->pluck('id')))
            ->whenOnlyPending(true)
            ->count();

        return view('portal::loan.manage.index', compact('user', 'employee', 'loans', 'pending_loans_count'));
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeLoan $loan, Request $request)
    {
        $user       = $request->user();
        $employee   = $user->employee;
        $results    = ApprovableResultEnum::cases();
        $loan       = $loan->load('approvables.userable.position');

        return view('portal::loan.manage.show', compact('user', 'employee', 'loan', 'results'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyApprovable $approvable, UpdateRequest $request)
    {
        $approvable->update($request->transformed()->toArray());

        $approved = $request->input('result') == ApprovableResultEnum::APPROVE->value;

        // Handle notifications
        if ($approved) {

            $approvable->modelable->update(['approved_at' => $approved && $approvable->is($approvable->modelable->approvables->sortByDesc('level')->first()) ? now() : null]);

            if ($approvable->modelable->childrens->count()) {
                $user = $approvable->userable_id;
                $approvable->modelable->childrens->load('approvables')->pluck('approvables')->flatten()->firstwhere('userable_id', $user)->update($request->transformed()->toArray());
                $approvable->modelable->childrens->first()->update(['approved_at' => $approved && $approvable->is($approvable->modelable->approvables->sortByDesc('level')->first()) ? now() : null]);
            }

            $approvable->modelable->employee->user->notify(new ApprovedNotification($approvable->modelable, $approvable));
            if ($superior = $approvable->modelable->approvables->sortBy('level')->filter(fn($a) => $a->level > $approvable->level)->first()) {
                $superior->userable->employee->user->notify(new SubmissionNotification($approvable->modelable, $approvable->userable));
            }

            if ($approvable->is($approvable->modelable->approvables->sortByDesc('level')->first())) {
                $position = EmployeePosition::active()->whereHas('position', fn($position) => $position->where('kd', 'finances-mgr'))->first();
                $position->employee->user->notify(new FinanceNotification($approvable->modelable, $approvable));
            }
        }

        if ($request->input('result') == ApprovableResultEnum::REJECT->value) {
            if ($approvable->modelable->childrens->count()) {
                $user = $approvable->userable_id;
                $approvable->modelable->childrens->load('approvables')->pluck('approvables')->flatten()->firstwhere('userable_id', $user)->update($request->transformed()->toArray());
            }
            $approvable->modelable->employee->user->notify(new RejectedNotification($approvable->modelable, $approvable));
        }

        return redirect()->next()->with('success', 'Berhasil memperbarui status pengajuan, terima kasih!');
    }

    public function destroy(EmployeeLoan $loan)
    {
        $this->authorize('destroy', $loan);

        $tmp = $loan;
        $loan->delete();

        return redirect()->back()->with('success', 'Pengajuan loan <strong>' . $tmp->employee->user->name . '</strong> berhasil dihapus');
    }
}

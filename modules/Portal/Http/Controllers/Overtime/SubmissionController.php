<?php

namespace Modules\Portal\Http\Controllers\Overtime;

use Illuminate\Http\Request;
use Modules\Account\Notifications\AccountNotification;
use Modules\Core\Enums\ApprovableResultEnum;
use Modules\Core\Enums\PositionLevelEnum;
use Modules\HRMS\Models\EmployeeOvertime;
use Modules\HRMS\Models\EmployeePosition;
use Modules\Portal\Http\Controllers\Controller;
use App\Notifications\GlobalGenericNotification;
use Modules\Portal\Http\Requests\Overtime\Submission\StoreRequest;
use Modules\Portal\Http\Requests\Overtime\Submission\UpdateRequest;
use Modules\Portal\Notifications\Overtime\Submission\SubmissionNotification;
use Modules\Portal\Notifications\Overtime\Submission\CanceledNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    private $superiors = [];

    private function sendOvertimeNotification($targetUser, $type, $overtime)
    {
        try {
            if (!$targetUser || !$targetUser->id) return;

            $user = auth()->user();

            $data = match($type) {
                'store' => [
                    'title'   => 'Pengajuan Lembur Baru',
                    'message' => "Karyawan <strong>{$user->name}</strong> mengajukan lembur baru. Mohon tinjau detailnya.",
                    'action'  => 'Persetujuan Lembur',
                    'icon'    => 'bx bx-timer',
                    'color'   => 'warning',
                    'link'    => route('portal::overtime.submission.index', ['view' => 'approvals']),
                ],
                'update' => [
                    'title'   => 'Perubahan Data Lembur',
                    'message' => "<strong>{$user->name}</strong> memperbarui data pengajuan lembur. Silakan cek kembali.",
                    'action'  => 'Cek Perubahan',
                    'icon'    => 'bx bx-edit-alt',
                    'color'   => 'info',
                    'link'    => route('portal::overtime.submission.index', ['view' => 'approvals']),
                ],
                'cancel' => [
                    'title'   => 'Lembur Dibatalkan',
                    'message' => "Pengajuan lembur atas nama <strong>{$user->name}</strong> telah dibatalkan oleh karyawan.",
                    'action'  => 'Izin Dibatalkan',
                    'icon'    => 'bx bx-trash-alt',
                    'color'   => 'secondary',
                    'link'    => '#',
                ],
                default => null,
            };

            if ($data) {
                $targetUser->sendSystemNotification([
                    'user_id_target' => $targetUser->id,
                    'title'          => $data['title'],
                    'message'        => $data['message'],
                    'action'         => $data['action'],
                    'link'           => $data['link'],
                    'icon'           => $data['icon'],
                    'color'          => $data['color'],
                    'sender_name'    => $user->name,
                    'sender_image'   => $user->image_url ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Realtime Overtime Notification Error: " . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;
        $view = $request->get('view', 'mine');

        $start_at = $request->get('start_at', date('Y-m-d'));
        $end_at   = $request->get('end_at', date('Y-m-d'));

        $query = EmployeeOvertime::query()
            ->withTrashed()
            ->with(['employee.user', 'approvables.userable.position']);

        if ($view === 'approvals') {
            $myPositionIds = $user->employee->positions()->pluck('id')->toArray();

            $query->whereHas('approvables', function($q) use ($myPositionIds) {
                $q->where('userable_type', EmployeePosition::class)
                ->whereIn('userable_id', $myPositionIds);
            });
        } else {
            $query->where('empl_id', $employee->id);
        }

        if ($request->filled('start_at')) {
            $query->where(function($q) use ($start_at, $end_at) {
                $q->whereDate('created_at', '>=', $start_at)
                ->whereDate('created_at', '<=', $end_at)
                ->orWhereRaw("exists (select 1 from jsonb_array_elements(dates::jsonb) as elem where (elem->>'d')::date >= ? and (elem->>'d')::date <= ?)", [$start_at, $end_at]);
            });
        }

        $overtimes = $query->latest()->paginate($request->get('limit', 10));

        $isApprover = false;
        if ($employee->position && $employee->position->position) {
            $isApprover = $employee->position->position->children()->exists();
        }

        return view('portal::overtime.submission.index', compact(
            'employee', 'overtimes', 'start_at', 'end_at', 'view', 'isApprover'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = $request->user()->employee->load([
            'position.position.parents' => function ($query) {
                $query->orderBy('level', 'desc')->with(['employeePositions' => function ($ep) {
                    $ep->active()->with('employee.user');
                }]);
            }
        ]);

        $superiors = [];

        $parentPositions = $employee->position->position->parents->sortByDesc('level');

        foreach ($parentPositions as $index => $position) {
            $hasEmployee = $position->employeePositions->isNotEmpty();

            if ($hasEmployee) {
                $superiors[] = [
                    'step'      => count($superiors) + 1,
                    'required'  => true,
                    'label'     => $position->name,
                    'positions' => collect([$position])
                ];
            }
        }

        return view('portal::overtime.submission.create', compact('employee', 'superiors'));
    }

    public function show(EmployeeOvertime $overtime, Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $overtime->load(['approvables.userable.position', 'employee.position.position.parents']);

        $superiors = [];
        if ($overtime->employee->position && $overtime->employee->position->position) {
            $parents = $overtime->employee->position->position->parents;
            foreach ($parents as $index => $parent) {
                $superiors[] = [
                    'step'  => $index + 1,
                    'id'    => $parent->id,
                    'label' => $parent->name
                ];
            }
        }


        return view('portal::overtime.submission.show', compact('user', 'employee', 'overtime', 'superiors'));
    }

    public function store(StoreRequest $request)
    {
        $employee = $request->user()->employee;

        DB::beginTransaction();

        try {
            $overtime = $employee->overtimes()->create($request->transform());
            $selectedApprovers = $request->input('approvables', []);

            if (!empty($selectedApprovers)) {
                krsort($selectedApprovers);
                foreach ($selectedApprovers as $employeePositionId) {
                    $approverPosition = EmployeePosition::active()->find($employeePositionId);
                    if ($approverPosition) {
                        $overtime->createApprovable($approverPosition, ['type' => 'approvable']);
                    }
                }
            }

            $overtime->load('approvables.userable.employee.user');
            $firstApprover = $overtime->approvables()->orderBy('level')->first();

            if ($firstApprover && $firstApprover->userable) {
                $targetUser = $firstApprover->userable->employee->user;
                $this->sendOvertimeNotification($targetUser, 'store', $overtime);
            } else {
                $overtime->update(['paidable_at' => now()]);
            }

            DB::commit();

            return redirect()->route('portal::overtime.submission.index')->with(
                'success',
                $overtime->approvables()->count() > 0
                    ? 'Pengajuan lembur sudah terkirim ke atasan terkait!'
                    : 'Pengajuan sudah tersimpan dan disetujui otomatis oleh sistem!'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan pengajuan lembur: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    /**
     * Update a resource in storage.
     */
    public function update(EmployeeOvertime $overtime, UpdateRequest $request)
    {
        $employee = $request->user()->employee;
        $overtime->fill($request->transformed()->toArray());

        if ($overtime->save()) {
            foreach (config('modules.core.features.services.overtimes.approvable_steps', []) as $index => $model) {
                if ($model['type'] == 'parent_position_level') {
                    $employee->position->position->parents->where('level.value', $model['value'])->each(
                        fn($position) =>
                        $overtime->createApprovable(empty($model['hide_from_input']) ? $position->employeePositions()->active()->find($request->input('approvables.' . $index)) : $position->employeePositions()->active()->first())
                    );
                }
            }

            if ($approvable = $overtime->approvables()->orderBy('level')->first()) {
                $targetUser = $approvable->userable->employee->user;
                $this->sendOvertimeNotification($targetUser, 'update', $overtime);
            } else {
                $overtime->update(['paidable_at' => now()]);
            }

            return redirect()->route('portal::overtime.submission.index')->with('success', 'Data berhasil diperbarui.');
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeOvertime $overtime)
    {
        $employeeName = $overtime->employee->user->name;
        $approvable = $overtime->approvables()->orderBy('level')->first();

        if ($approvable && $approvable->userable) {
            $targetUser = $approvable->userable->employee->user;
            $this->sendOvertimeNotification($targetUser, 'cancel', $overtime);
        }

        $overtime->delete();

        return redirect()->route('portal::overtime.submission.index')->with('success', 'Pengajuan telah dibatalkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function approve(EmployeeOvertime $overtime, Request $request)
    {
        $employee = $request->user()->employee;
        $myApproval = $overtime->approvables()
            ->where('userable_id', $employee->position->id)
            ->first();

        if (!$myApproval || $myApproval->result->value != 0) {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses atau Anda tidak memiliki akses.');
        }

        $myApproval->update([
            'result' => 1,
            'approved_at' => now()
        ]);

        $overtime->update(['accepted_at' => now()]);

        return redirect()->back()->with('success', 'Berhasil menyetujui pengajuan lembur.');
    }

    public function export()
    {
        return 'ok';
    }
}

<?php

namespace Modules\HRMS\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Account\Enums\SexEnum;
use Modules\Core\Models\CompanyVacationCategory;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeContract;
use Modules\HRMS\Models\EmployeeVacationQuota;

trait EmployeeVacationQuotaRepository
{
    /**
     * Store newly created resource.
     */
    public function storeEmployeeVacationQuota(array $data)
    {
        $employee = Employee::find($data['empl_id']);

        if ($employee->vacationQuotas()->saveMany(array_map(fn($quota) => new EmployeeVacationQuota($quota), $data['quotas']))) {
            return $employee;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyEmployeeVacationQuota(EmployeeVacationQuota $quota)
    {
        if ($quota->delete()) {
            return $quota;
        }
        return false;
    }

    /**
     * generate vacation this year.
     */
    public function generateVacationThisYear($year)
    {
        $currentDate = date($year . '-m-d');
        $start_at = Carbon::parse($currentDate)->startOfYear();
        $end_at = Carbon::parse($currentDate)->endOfYear();

        DB::beginTransaction();

        try {
            $employees = Employee::with('position.position', 'user.meta')
                ->whereHas('contract')
                ->get();

            foreach ($employees as $employee) {
                $quota = $employee->vacationQuotas()
                    ->whereDate('start_at', $start_at->format('Y-m-d'))
                    ->whereDate('end_at', $end_at->format('Y-m-d'))
                    ->first();

                if (empty($quota)) {
                    $dataQuota = $this->addQuotaByGender($employee->user->getMeta('profile_sex'), $start_at, $end_at);
                    $employee->vacationQuotas()->createMany($dataQuota);
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('danger', 'Terjadi kegagalan, error code:' . $th->getMessage());
        }
    }

    public function addQuotaByGender($gender, $start_at, $end_at): array
    {
        switch ($gender) {
            case SexEnum::FEMALE->value:
                $ctgs = CompanyVacationCategory::all();
                break;

            default:
                $ctgs = CompanyVacationCategory::whereIn('id', [1, 4])->get();
                break;
        }

        foreach ($ctgs as $key => $value) {
            $quota[$key] = [
                'start_at' => $start_at,
                'end_at' => $end_at,
                'ctg_id' => $value->id,
                'quota' => $value->meta?->quota,
                'visible_at' => $start_at,
            ];
        }

        return $quota;
    }
}

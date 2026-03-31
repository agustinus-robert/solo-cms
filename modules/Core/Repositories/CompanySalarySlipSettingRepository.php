<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Arr;
use Modules\Account\Models\User;
use Modules\Core\Models\CompanyPayrollSetting;

trait CompanySalarySlipSettingRepository
{
    /**
     * Store newly created resource.
     */
    public function storePayrollConfig(array $data, User $user)
    {
        $setting = new CompanyPayrollSetting(
            Arr::only($data, ['key', 'az', 'meta']),
        );

        if ($setting->save()) {
            return $setting;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyPayrollConfig(CompanyPayrollSetting $setting, User $user)
    {
        if (!$setting->trashed() && $setting->delete()) {
            return $setting;
        }
        return false;
    }
}

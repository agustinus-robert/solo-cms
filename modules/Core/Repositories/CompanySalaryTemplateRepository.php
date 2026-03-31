<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Auth;
use Modules\Account\Models\User;
use Modules\Core\Models\CompanySalaryTemplate;

trait CompanySalaryTemplateRepository
{
    /**
     * Store newly created resource.
     */
    public function storeCompanySalaryTemplate(array $data, User $user)
    {
        $template = new CompanySalaryTemplate($data);
        if ($template->save()) {
            return $template;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanySalaryTemplate(CompanySalaryTemplate $template, array $data, User $user)
    {
        $template = $template->fill($data);
        if ($template->save()) {
            return $template;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanySalaryTemplate(CompanySalaryTemplate $template, User $user)
    {
        if (!$template->trashed() && $template->delete()) {
            return $template;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanySalaryTemplate(CompanySalaryTemplate $template, User $user)
    {
        if ($template->trashed() && $template->restore()) {
            return $template;
        }
        return false;
    }
}

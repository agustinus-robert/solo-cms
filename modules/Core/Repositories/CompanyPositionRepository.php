<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Modules\Core\Models\CompanyPosition;

trait CompanyPositionRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'kd', 'name', 'description', 'level', 'dept_id', 'is_visible', 'position_type_id'
    ];

    /**
     * Store newly created resource.
     */
    public function storeCompanyPosition(array $data)
    {
        $position = new CompanyPosition(Arr::only($data, $this->keys));
        if ($position->save()) {

            if (isset($data['default_applied_role'])) {
                $position->setMeta('default_applied_role', $data['default_applied_role']);
            }

            $position->parents()->sync($data['parents'] ?? []);
            $position->children()->sync($data['children'] ?? []);

            return $position;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyPosition(CompanyPosition $position, array $data)
    {
        $position->fill(Arr::only($data, $this->keys));

        if ($position->save()) {
            $position->setMeta('default_applied_role', $data['default_applied_role'] ?? null);

            $position->parents()->sync($data['parents'] ?? []);
            $position->children()->sync($data['children'] ?? []);

            return $position;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanyPosition(CompanyPosition $position)
    {
        if (!$position->trashed() && $position->delete()) {
            return $position;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanyPosition(CompanyPosition $position)
    {
        if ($position->trashed() && $position->restore()) {
            return $position;
        }
        return false;
    }
}

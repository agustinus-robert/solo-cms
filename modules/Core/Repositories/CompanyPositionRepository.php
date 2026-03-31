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
        'kd', 'name', 'description', 'level', 'dept_id', 'is_visible'
    ];

    /**
     * Store newly created resource.
     */
    public function storeCompanyPosition(array $data)
    {
        $position = new CompanyPosition(Arr::only($data, $this->keys));
        if ($position->save()) {
            $position->parents()->sync($data['parents']);
            if (isset($data['children'])) {
                $position->children()->sync($data['children']);
            }
            return $position;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyPosition(CompanyPosition $position, array $data)
    {
        $position = $position->fill(Arr::only($data, $this->keys));
        if ($position->save()) {
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

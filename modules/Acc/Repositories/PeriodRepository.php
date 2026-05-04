<?php

namespace Modules\Acc\Repositories;

use Modules\Acc\Models\Period;

trait PeriodRepository
{
    public function getAll(array $params)
    {
        return Period::query()
            ->when(isset($params['search']), function ($q) use ($params) {
                $q->where('name', 'ilike', "%{$params['search']}%");
            })
            ->orderBy('start_date', 'desc')
            ->paginate(15);
    }

    public function upsert(array $data, $id = null)
    {
        return Period::updateOrCreate(
            ['id' => $id],
            $data
        );
    }

    public function delete($id)
    {
        return Period::destroy($id);
    }
}

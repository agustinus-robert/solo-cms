<?php

namespace Modules\Hotel\Repositories\Master;

use Modules\Hotel\Models\BookingSource;

trait SourceRepositories
{
    public function upsertSource(array $data, ?int $id = null): BookingSource
    {
        return BookingSource::updateOrCreate(
            ['id' => $id],
            [
                'name'            => $data['name'],
                'commission_rate' => $data['commission_rate'] ?? 0,
            ]
        );
    }

    public function deleteSource(int $id): bool
    {
        return BookingSource::findOrFail($id)->delete();
    }
}

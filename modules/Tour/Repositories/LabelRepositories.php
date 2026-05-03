<?php

namespace Modules\Tour\Repositories;

use Modules\Tour\Models\TourLabel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait LabelRepositories
{
    public function upsertLabel(array $data, TourLabel $label = null)
    {
        return DB::transaction(function () use ($data, $label) {
            $data['slug'] = Str::slug($data['name']);

            if ($label) {
                $label->update($data);
                return $label;
            }

            return TourLabel::create($data);
        });
    }

    public function getLabelTable($request)
    {
        return TourLabel::when($request->search, function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);
    }
}

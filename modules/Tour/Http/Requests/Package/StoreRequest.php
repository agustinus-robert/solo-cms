<?php

namespace Modules\Tour\Http\Requests\Package;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tour_id'           => 'required|exists:tours,id',
            'package_name'      => 'required|string|max:255',
            'price_per_person'  => 'required|numeric|min:0',
            'label_ids'         => 'nullable|array',
            'label_ids.*'       => 'exists:tour_labels,id'
        ];
    }

    public function transform(): array
    {
        return [
            'tour_id'           => $this->tour_id,
            'package_name'      => $this->package_name,
            'price_per_person'  => $this->price_per_person,
        ];
    }
}

<?php

namespace Modules\Tour\Http\Requests\Availability;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tour_package_id' => 'required|exists:tour_packages,id',
            'available_date'  => 'required|date',
            'stock'           => 'required|integer|min:0',
            'is_available'    => 'nullable|boolean',
        ];
    }

    public function transform(): array
    {
        return [
            'tour_package_id' => $this->input('tour_package_id'),
            'available_date'  => $this->input('available_date'),
            'stock'           => $this->input('stock'),
            'is_available'    => $this->filled('is_available') ? $this->boolean('is_available') : true,
        ];
    }
}

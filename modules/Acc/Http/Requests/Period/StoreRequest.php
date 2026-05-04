<?php

namespace Modules\Acc\Http\Requests\Period;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ];
    }

    public function transform(): array
    {
        return [
            'name'       => $this->input('name'),
            'start_date' => $this->input('start_date'),
            'end_date'   => $this->input('end_date'),
            'is_closed'  => false,
        ];
    }
}

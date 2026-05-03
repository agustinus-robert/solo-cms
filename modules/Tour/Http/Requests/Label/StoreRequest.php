<?php

namespace Modules\Tour\Http\Requests\Label;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
        ];
    }

    public function transform(): array
    {
        return [
            'name'  => $this->input('name'),
            'icon'  => $this->input('icon') ?? 'mdi-tag',
            'color' => $this->input('color') ?? '#6c757d',
        ];
    }
}

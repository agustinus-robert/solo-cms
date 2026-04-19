<?php

namespace Modules\Hotel\Http\Requests\Source;

use Modules\Hotel\Http\Requests\Source\StoreRequest;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ];
    }
}

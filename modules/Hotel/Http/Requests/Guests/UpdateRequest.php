<?php

namespace Modules\Hotel\Http\Requests\Guests;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanyBuilding;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'id_card_number' => 'required',
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'nullable|string|max:100',
            'phone_number'   => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
        ];
    }

}

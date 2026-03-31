<?php

namespace Modules\Core\Http\Requests\Company\Asset\Building;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanyBuilding;

class UpdateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'kd'                => 'required|max:255|string',
            'name'              => 'required|max:255|string',
            'address_primary'   => 'required|max:255|string',
            'address_secondary' => 'required|max:255|string',
            'address_city'      => 'required|max:255|string',
            'state_id'          => 'required|numeric'
        ];
    }
}

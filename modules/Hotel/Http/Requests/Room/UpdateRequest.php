<?php

namespace Modules\Hotel\Http\Requests\Room;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanyBuilding;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'room_number'  => 'required',
            'room_type_id' => 'required|exists:hotel_room_types,id',
            'floor'        => 'required|integer|min:1',
            'status'       => 'required|integer',
        ];
    }
}

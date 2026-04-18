<?php

namespace Modules\Hotel\Http\Requests\Room;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanyInsurancePrice;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeInsurance;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'room_number'  => 'required|unique:hotel_rooms,room_number',
            'room_type_id' => 'required|exists:hotel_room_types,id',
            'floor'        => 'required|integer|min:1',
            'status'       => 'required|integer',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'room_number' => "Nomor Ruangan",
            'room_type_id' => "Tipe Ruangan",
            'floor' => 'Lantai',
            'status' => 'Status Kamar'
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        return [
            'room_number' => $this->input('room_number'),
            'room_type_id' => $this->input('room_type_id'),
            'floor' => $this->input('floor'),
            'status' => $this->input('status'),
        ];
    }
}

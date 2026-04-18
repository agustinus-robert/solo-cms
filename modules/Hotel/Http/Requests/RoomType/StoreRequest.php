<?php

namespace Modules\Hotel\Http\Requests\RoomType;

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
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'name' => "Nama Tipe",
            'base_price' => "Harga Tipe",
            'capacity' => "Kapsitas Kamar",
            'description' => "Deskripsi"
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        return [
            'name' => $this->input('name'),
            'base_price' => $this->input('base_price'),
            'capacity' => $this->input('capacity'),
            'description' => $this->input('description')
        ];
    }
}

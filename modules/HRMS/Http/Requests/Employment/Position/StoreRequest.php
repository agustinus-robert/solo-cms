<?php

namespace Modules\HRMS\Http\Requests\Employment\Position;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanyPosition;
use Modules\HRMS\Models\EmployeePosition;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'position_id'       => 'required|string|max:191|exists:' . (new CompanyPosition())->getTable() . ',id',
            'start_at'          => 'sometimes|date',
            'end_at'            => 'nullable|date|after:start_at'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'position_id'       => 'nama jabatan',
            'start_at'          => 'tanggal berlaku',
            'end_at'            => 'tanggal berakhir'
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        return $this->validated();
    }
}

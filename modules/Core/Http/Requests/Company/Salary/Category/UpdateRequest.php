<?php

namespace Modules\Core\Http\Requests\Company\Salary\Category;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanySalarySlip;
use Modules\Core\Models\CompanySalarySlipCategory;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'az'        => 'required|unique:' . (new CompanySalarySlipCategory)->getTable() . ',az,' . $this->category->id . '|numeric|between:1,200',
            'slip_id'   => 'required|numeric|exists:' . (new CompanySalarySlip)->getTable() . ',id',
            'name'      => 'required|max:191|string'
        ];
    }
}

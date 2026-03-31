<?php

namespace Modules\Core\Http\Requests\Company\Salary\Component;

use Modules\Core\Models\CompanySalarySlipCategory;

class UpdateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'ctg_id'  => 'required|numeric|exists:' . (new CompanySalarySlipCategory)->getTable() . ',id',
            'unit'    => 'required|numeric',
            'name'    => 'required|max:191|string',
            'operate' => 'nullable'
        ];
    }
}

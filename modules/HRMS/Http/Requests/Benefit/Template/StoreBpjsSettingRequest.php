<?php

namespace Modules\HRMS\Http\Requests\Benefit\Template;

use App\Http\Requests\FormRequest;

class StoreBpjsSettingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'max_salary'            => 'required|numeric|min:0',
            'min_salary'            => 'required|numeric|min:0|lte:max_salary',
            'max_tk_pensiun_salary' => 'required|numeric|min:0',
            'min_tk_salary'         => 'required|numeric|min:0|lte:max_tk_pensiun_salary',
            'limit_salary'          => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'numeric' => ':attribute harus berupa angka yang valid.',
            'lte'     => ':attribute harus lebih kecil atau sama dengan :value.',
        ];
    }
}

<?php

namespace Modules\Finance\Http\Requests\Tax\Template;

use App\Http\Requests\FormRequest;
use Modules\Core\Models\CompanySalarySlipComponent;
use Modules\HRMS\Enums\TaxObjectEnum;

class ConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'rate.employee'  => 'required|numeric|max: 100',
            'rate.company'   => 'required|numeric|max: 100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'rate.employee' => 'ditanggung karyawan',
            'rate.company'  => 'ditanggung perusahaan',
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        foreach (TaxObjectEnum::cases() as $key => $value) {
            $data[$value->key()] = [
                'label' => $value->label(),
                'key' => $value->key(),
                'rate' => $this->input('rate.' . $value->key()),
            ];
        }

        return [
            'key' => 'cmp_pph_objective_percentage',
            'components' => $data
        ];
    }
}

<?php

namespace Modules\Finance\Http\Requests\Service\Deduction\Manage;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return isset($this->user()->employee);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'information' => 'required',
            'price' => 'required'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'information' => 'informasi harus diisi',
            'price' => 'harga harus diisi'
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        $field = count($this->input('field'));

        $deductionArr = [];
        $deductionGrandTotal = [];
        $totalDeduction = 0;
        for ($i = 0; $i < $field; $i++) {
            $deductionArr["field{$i}"] = [
                'name' => $this->input('information')[$i],
                'price' => (int) $this->input('price')[$i],
            ];

            $totalDeduction += (int) $this->input('price')[$i];
        }
        $deductionGrandTotal['total'] = $totalDeduction;

        return [
            ...$this->only('type', 'start_at', 'end_at', 'information', 'price'),
            'empl_id' => $this->input('empl_id'),
            'result' => array_merge(
                $deductionArr,
                $deductionGrandTotal
            )
        ];
    }
}

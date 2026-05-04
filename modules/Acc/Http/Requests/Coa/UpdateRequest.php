<?php

namespace Modules\Acc\Http\Requests\Coa;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('coa');
        if (is_object($id)) $id = $id->id;

        return [
            'code' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('acc_coas', 'code')->ignore($id)
            ],
            'name'           => 'required|string|max:255',
            'category'       => 'required|string',
            'normal_balance' => 'required|string|in:debit,credit',
        ];
    }

    public function transform(): array
    {
        return [
            'code'           => $this->input('code'),
            'name'           => $this->input('name'),
            'category'       => $this->input('category'),
            'normal_balance' => $this->input('normal_balance'),
            'is_active'      => $this->boolean('is_active', true),
        ];
    }
}

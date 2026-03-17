<?php

namespace Modules\Poz\Http\Requests;

use Auth;
use App\Http\Requests\FormRequest;


class TierTransactionStoreRequest extends FormRequest
{
    public $placeable;

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
    public function rules(): array
    {
        return [
            'form.name' => 'required',
            'form.transaction_tier_id' => 'required'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'form.name' => 'Nama Tier',
            'form.transaction_tier_id' => 'Tier Harus dipilih'
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required' => ':attribute harus diisi',
            'form.transaction_tier_id.required' => ':attribute harus dipilih',
        ];
    }
}

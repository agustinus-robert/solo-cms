<?php

namespace Modules\Acc\Http\Requests\Coa;

use Modules\Acc\Enums\NormalBalance;
use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id'       => 'nullable|integer',
            'code'     => 'required|string|unique:acc_coas,code',
            'name'     => 'required|string|max:255',
            'category' => 'required|string',
            'is_active'=> 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'code'     => 'Kode Akun',
            'name'     => 'Nama Akun',
            'category' => 'Kategori',
        ];
    }

    /**
     * Transformasi data sebelum masuk ke repository.
     */
    public function transform(): array
    {
       $category = $this->input('category');
       $normalBalance = in_array($category, ['asset', 'expense'])
            ? NormalBalance::DEBIT->value
            : NormalBalance::CREDIT->value;

        return [
            'code'           => $this->input('code'),
            'name'           => $this->input('name'),
            'category'       => $category,
            'normal_balance' => $normalBalance,
            'is_active'      => $this->boolean('is_active', true),
        ];
    }
}

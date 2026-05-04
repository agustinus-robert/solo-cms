<?php

namespace Modules\Acc\Http\Requests\BeginningBalance;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'period_id' => 'required|exists:acc_periods,id',
            'balances'  => 'required|array',
            'balances.*.coa_id' => 'required|exists:acc_coas,id',
            'balances.*.amount' => 'required|numeric',
        ];
    }

    public function transform(): array
    {
        // Mengembalikan array yang sudah rapi untuk diproses repository
        return collect($this->balances)->map(function($item) {
            return [
                'period_id' => $this->period_id,
                'coa_id'    => $item['coa_id'],
                'amount'    => $item['amount'] ?? 0,
            ];
        })->toArray();
    }
}

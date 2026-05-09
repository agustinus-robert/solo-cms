<?php

namespace Modules\Acc\Http\Requests\Ledger;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transaction_date' => 'required|date',
            'reference_number' => 'required|string|unique:acc_ledgers,reference_number,' . ($this->route('ledger') ? $this->route('ledger')->id : 'NULL'),
            'description'      => 'nullable|string',
            'entries'          => 'required|array|min:2',
            'entries.*.coa_id' => 'required|exists:acc_coas,id',
            'entries.*.debit'  => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
            'type' => ['required', \Illuminate\Validation\Rule::enum(\Modules\Acc\Enums\LedgerType::class)],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totalDebit = collect($this->entries)->sum('debit');
            $totalCredit = collect($this->entries)->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                $validator->errors()->add('balance', "Jurnal tidak balance! Selisih: " . ($totalDebit - $totalCredit));
            }
        });
    }

    public function transform(): array
    {
        return [
            'transaction_date' => $this->transaction_date,
            'reference_number' => $this->reference_number,
            'description'      => $this->description,
            'source_module'    => 'manual',
            'type'             => $this->type,
            'entries'          => collect($this->entries)->map(function($e) {
                return [
                    'coa_id' => $e['coa_id'],
                    'debit'  => $e['debit'] ?? 0,
                    'credit' => $e['credit'] ?? 0,
                ];
            })->toArray()
        ];
    }
}

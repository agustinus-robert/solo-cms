<?php

namespace Modules\Acc\Repositories;

use Modules\Acc\Models\Ledger;
use Illuminate\Support\Facades\DB;

trait LedgerRepository
{
    public function getAll(array $params)
    {
        return Ledger::query()
            ->with(['ledgerEntries.coa', 'user'])
            ->when(isset($params['search']), function ($q) use ($params) {
                $q->where(function($sub) use ($params) {
                    $sub->where('reference_number', 'ilike', "%{$params['search']}%")
                        ->orWhere('description', 'ilike', "%{$params['search']}%");
                });
            })
            ->when(!empty($params['type']), function ($q) use ($params) {
                $q->where('type', $params['type']);
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(15)
            ->withQueryString();
    }

    public function upsert(array $data, $id = null)
    {
        return DB::transaction(function () use ($data, $id) {
            $payload = [
                'transaction_date' => $data['transaction_date'],
                'reference_number' => $data['reference_number'],
                'description'      => $data['description'],
                'source_module'    => $data['source_module'] ?? 'manual',
                'user_id'          => auth()->id(),
                'type'             => $data['type'] ?? 'general',
            ];

            if (!empty($id) && is_numeric($id)) {
                $ledger = Ledger::findOrFail($id);
                $ledger->update($payload);
            } else {
                $ledger = Ledger::create($payload);
            }

            if (isset($data['entries'])) {
                $ledger->ledgerEntries()->delete();
                $ledger->ledgerEntries()->createMany($data['entries']);
            }

            return $ledger;
        });
    }

    public function delete($id)
    {
        return Ledger::destroy($id);
    }
}

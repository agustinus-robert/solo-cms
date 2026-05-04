<?php

namespace Modules\Acc\Repositories;

use Modules\Acc\Models\Coa;

trait CoaRepository
{
    /**
     * Mengambil semua data COA dengan fitur pencarian dan pagination.
     */
    public function getAll(array $params)
    {
        return Coa::query()
            ->when(isset($params['search']), function ($q) use ($params) {
                $search = $params['search'];
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(15);
    }

    /**
     * Mencari satu data berdasarkan ID.
     */
    public function find($id)
    {
        return Coa::findOrFail($id);
    }

    /**
     * Proses Simpan atau Update (Upsert).
     */
    public function upsert(array $data, $id = null)
    {
        unset($data['id']);

        return Coa::updateOrCreate(
            ['id' => $id], // Cari berdasarkan ID ini
            $data          // Update data ini
        );
    }

    /**
     * Menghapus data.
     */
    public function delete($id)
    {
        $coa = $this->find($id);
        return $coa->delete();
    }
}

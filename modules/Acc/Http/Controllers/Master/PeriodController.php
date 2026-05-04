<?php

namespace Modules\Acc\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Period;
use Modules\Acc\Repositories\PeriodRepository;
use Modules\Acc\Http\Requests\Period\StoreRequest;
use Modules\Acc\Http\Requests\Period\UpdateRequest;

class PeriodController extends Controller
{
    use PeriodRepository;

    /**
     * Tampilkan daftar periode akuntansi.
     */
    public function index(Request $request)
    {
        $periods = $this->getAll($request->all());

        return view('acc::period.index', compact('periods'));
    }

    /**
     * Form tambah periode (jika tidak pakai modal).
     */
    public function create()
    {
        $period = null;
        return view('acc::period.upsert', compact('period'));
    }

    /**
     * Simpan periode baru.
     */
    public function store(StoreRequest $request)
    {
        $this->upsert($request->transform());

        return redirect()
            ->route('acc::period.index')
            ->with('success', 'Periode akuntansi berhasil dibuat.');
    }

    /**
     * Form edit periode.
     */
    public function edit(Period $period)
    {
        return view('acc::period.upsert', compact('period'));
    }

    /**
     * Update data periode.
     */
    public function update(UpdateRequest $request, Period $period)
    {
        // Lempar ID eksplisit agar Eloquent melakukan UPDATE (Postgres Safe)
        $this->upsert($request->transform(), $period->id);

        return redirect()
            ->route('acc::period.index')
            ->with('success', 'Periode akuntansi berhasil diperbarui.');
    }

    /**
     * Hapus periode.
     */
    public function destroy(Period $period)
    {
        // Tambahkan pengecekan jika sudah ada transaksi di ledger sebelum hapus
        $this->delete($period->id);

        return redirect()
            ->route('acc::period.index')
            ->with('success', 'Periode berhasil dihapus.');
    }

    /**
     * Shortcut untuk tutup/buka periode secara cepat.
     */
    public function toggleStatus(Period $period)
    {
        $period->update([
            'is_closed' => !$period->is_closed
        ]);

        return redirect()->back()->with('success', 'Status periode berhasil diubah.');
    }
}

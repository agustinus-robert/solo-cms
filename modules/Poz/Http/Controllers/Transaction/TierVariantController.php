<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\TierTransaction;
use Modules\Poz\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TierVariantController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function index()
    {
        $data = [];

        $data['column'] = [
            dbuilder_table('name', 'Nama', false, true),
            dbuilder_table('tier', 'Kategori Tier', false, true),
            dbuilder_table('action', 'Aksi')
        ];

        $data['title'] = 'Daftar Tier Variant';

        return view('poz::transaction.tiers', $data);
    }

    public function create(Request $request)
    {

        return view('poz::transaction.tiers', [
            'action' => 'Create'
        ]);
    }

    public function edit(Request $request)
    {

        return view('poz::transaction.tiers', [
            'action' => 'Update'
        ]);
    }

    public function destroy(Request $request)
    {
        $tier = TierTransaction::findOrFail($request->tier); // Mencari pozt berdasarkan ID
        $tier->delete(); // Melakukan soft delete

        return redirect(route('poz::transaction.tier-variant.index'))->with('msg-sukses', "Data berhasil dihapus");
    }

    public function tierTable(Request $request)
    {
        $outletId = $request->outlet;
        $tier = TierTransaction::with('user', 'outlets')
            ->whereNull('deleted_at')
            ->whereHas('outlets', function ($query) use ($outletId) {
                $query->where('outlet_id', $outletId);
            });

        if (!empty($search = $request->search)) {
            $tier->where(function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%");
            });
        }

        if (!empty($order = $request->filter)) {
            if ($order === 'new') {
                $tier->orderBy('id', 'desc');
            } elseif ($order === 'old') {
                $tier->orderBy('id', 'asc');
            }
        }


        return Table::of($tier)
            ->addIndexColumn()
            ->addColumn('tier', function ($row) use ($request) {
                return $row->tiers->name;
            })
            ->addColumn('action', function ($row) use ($request) {
                $template = '';
                $outletId = $request->outlet;

                $template .= view('poz::layouts_master.component.button_edit', array('id' => $row->id, 'update' => route('poz::transaction.tier-variant.edit', ['tier_variant' => $row->id]) . '?outlet=' . $outletId))->render();
                $template .= view('poz::layouts_master.component.button_delete', array('id' => $row->id, 'delete' => route('poz::transaction.tier-variant.destroy', ['tier_variant' => $row->id]) . '?outlet=' . $outletId))->render();


                return $template;
            })
            ->rawColumns(['image', 'action'])->make(true);
    }
}

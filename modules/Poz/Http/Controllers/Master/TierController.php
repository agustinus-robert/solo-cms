<?php

namespace Modules\Poz\Http\Controllers\Master;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\Tier;
use Modules\Poz\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TierController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function index()
    {
        $data = [];
        // dbuilder_table untuk membuat generate table pada kolom header dan pemanggilan kolom database
        $data['column'] = [
            //DT_RowIndex usahakan false karena tidak ada secara fisik pada database
            dbuilder_table('image', 'Gambar', false, false, 'w10'),
            dbuilder_table('name', 'Nama', false, true),
            dbuilder_table('action', 'Aksi')
        ];

        $data['title'] = 'Daftar Tier';

        return view('poz::master.tier.index', $data);
    }

    public function create(Request $request)
    {

        return view('poz::master.tier.index', [
            'action' => 'Create'
        ]);
    }

    public function edit(Request $request)
    {

        return view('poz::master.tier.index', [
            'action' => 'Update'
        ]);
    }

    public function destroy(Request $request)
    {
        $tier = Tier::findOrFail($request->tier); // Mencari pozt berdasarkan ID
        $tier->delete(); // Melakukan soft delete

        return redirect(route('poz::master.tier.index'))->with('msg-sukses', "Data berhasil dihapus");
    }

    public function tierTable(Request $request)
    {
        $outletId = $request->outlet;
        $tier = Tier::with('user', 'outlets')
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
            ->addColumn('image', function ($row) {
                if (!empty($row->location) && !empty($row->image_name)) {
                    $image = $row->location . '/' . $row->image_name;

                    return "<img width='50' height='50' src='" . asset('uploads/' . $image) . "' />";
                } else {
                    return "<img src='https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg?20200913095930' width='50' height='50' />";
                }
            })
            ->addColumn('action', function ($row) use ($request) {
                $template = '';
                $outletId = $request->outlet;

                $template .= view('poz::layouts_master.component.button_edit', array('id' => $row->id, 'update' => route('poz::master.tier.edit', ['tier' => $row->id]) . '?outlet=' . $outletId))->render();
                $template .= view('poz::layouts_master.component.button_delete', array('id' => $row->id, 'delete' => route('poz::master.tier.destroy', ['tier' => $row->id]) . '?outlet=' . $outletId))->render();


                return $template;
            })
            ->rawColumns(['image', 'action'])->make(true);
    }
}

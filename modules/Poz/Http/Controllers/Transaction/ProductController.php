<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\TierTransaction;
use Modules\Poz\Models\Tier;
use Modules\Poz\Models\Brand;
use Modules\Poz\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
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
            dbuilder_table('gambar', 'Gambar', true, false),
            dbuilder_table('code', 'Code', false, true),
            dbuilder_table('name', 'Nama', false, true),
            dbuilder_table('price', 'Harga', true, false),
            dbuilder_table('brand_id', 'Merek'),
            dbuilder_table('category_id', 'Kategori'),
            dbuilder_table('action', 'Aksi')
        ];

        return view('poz::transaction.product', $data);
    }

    public function create(Request $request)
    {

        return view('poz::transaction.product', [
            'action' => 'Create'
        ]);
    }

    public function show($product)
    {
        $product = Product::with(['metas', 'brand', 'category'])->findOrFail($product);

        $tier1 = '';
        $tier2 = '';
        $tierCount = $product->getMeta('tier_count', 1);
        $tier1Name = $product->getMeta('tier_name_1', 'Pilihan 1');
        $tier2Name = $product->getMeta('tier_name_2', 'Pilihan 2');

        $tier1 = Tier::find($tier1Name);
        $options1 = TierTransaction::whereHas('tiers', function($q) use ($tier1Name) {
                        $q->where('id', $tier1Name);
                    })->get();

        $options2 = [];
        if ($tierCount == 2) {
            $tier2 = Tier::find($tier2Name);
            $options2 = TierTransaction::whereHas('tiers', function($q) use ($tier2Name) {
                            $q->where('id', $tier2Name);
                        })->get();
        }

        return view('poz::transaction.product.show', [
            'product'    => $product,
            'tierCount'  => $tierCount,
            'tier1Name'  => $tier1Name,
            'tier2Name'  => $tier2Name,
            'options1'   => $options1,
            'options2'   => $options2,
            'tier1'      => $tier1,
            'tier2'      => $tier2,
            'action'     => 'Show'
        ]);
    }

    public function edit(Request $request)
    {

        return view('poz::transaction.product', [
            'action' => 'Update'
        ]);
    }

    public function destroy(Request $request)
    {
        $brand = Product::findOrFail($request->product); // Mencari pozt berdasarkan ID
        $brand->delete(); // Melakukan soft delete

        return redirect(route('poz::transaction.product.index'))->with('msg-sukses', "Data berhasil dihapus");
    }

    public function productTable(Request $request)
    {
        $outletId = $request->outlet;
        $product = Product::with('brand', 'category', 'user', 'outlets')
            ->whereNull('deleted_at')
            ->whereHas('outlets', function ($query) use ($outletId) {
                $query->where('outlet_id', $outletId);
            });

        if (!empty($search = $request->search)) {
            $product->where(function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%");
            });
        }

        if (!empty($order = $request->filter) && !is_array($order)) {
            if ($order === 'new') {
                $product->orderBy('id', 'desc');
            } elseif ($order === 'old') {
                $product->orderBy('id', 'asc');
            }
        }

        return Table::of($product)
            ->addIndexColumn()
            ->addColumn('gambar', function ($row) {
                $image = $row->location . '/' . $row->image_name;

                return "<img width='50' height='50' src='" . asset('uploads/' . $image) . "' />";
            })
            ->addColumn('price', function ($row) {
                return  $row->price;
            })
            ->addColumn('brand_id', function ($row) {
                return optional($row->brand)->name ?? 'unbranded';
            })
            ->addColumn('category_id', function ($row) {
                return optional($row->category)->name ?? 'unbranded';
            })
            ->addColumn('action', function ($row) use ($request) {
                $template = '';
                $outletId = $request->outlet;


                // $template .= view('poz::layouts_master.component.button_detail', array('id' => $row->id))->render();
                $template .= view('poz::layouts_master.component.button_show', array('id' => $row->id, 'show' => route('poz::transaction.product-variant.show', ['product_variant' => $row->id]) . '?outlet=' . $outletId))->render();
                $template .= view('poz::layouts_master.component.button_edit', array('id' => $row->id, 'update' => route('poz::transaction.product.edit', ['product' => $row->id]) . '?outlet=' . $outletId))->render();
                $template .= view('poz::layouts_master.component.button_delete', array('id' => $row->id, 'delete' => route('poz::transaction.product.destroy', ['product' => $row->id]) . '?outlet=' . $outletId))->render();


                return $template;
            })
            ->rawColumns(['gambar', 'action'])->make(true);
    }
}

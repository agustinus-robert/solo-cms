<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Sale as SaleData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Poz\Traits\SaleTrait;

class SaleController extends Controller
{
    use SaleTrait;

    public function index()
    {
        $data = [];
        $data['column'] = [
            dbuilder_table('reference', 'Nomor Referensi', false, true),
            dbuilder_table('sale_status', 'Status Penjualan', true, false),
            dbuilder_table('grand_total', 'Total'),
        ];

        return view('poz::transaction.sale', $data);
    }

    public function create(Request $request)
    {
        $outletId = $request->query('outlet', auth()->user()->current_outlet_id);

        $products = Product::whereHas('outlets', fn($q) => $q->where('outlet_id', $outletId))
            ->get()
            ->map(function($p) use ($outletId) {
                $p->stok_tersedia = $this->getAvailableStock($p->id, $outletId);
                return $p;
            });

        return view('poz::transaction.sale', [
            'action' => 'Buat',
            'outletId' => $outletId,
            'products' => $products,
            'sale' => null,
            'selectedItems' => []
        ]);
    }

    public function edit(Request $request)
    {
        $sale = SaleData::with('saleItems.product')->findOrFail($request->sale);
        $outletId = $request->query('outlet', auth()->user()->current_outlet_id);

        $products = Product::whereHas('outlets', fn($q) => $q->where('outlet_id', $outletId))
            ->get()
            ->map(function($p) use ($outletId) {
                $p->stok_tersedia = $this->getAvailableStock($p->id, $outletId);
                return $p;
            });

        $selectedItems = $sale->saleItems->map(function($item) use ($outletId) {
            return [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'qty' => $item->qty,
                'price' => (int)$item->price,
                // Gunakan fungsi langsung karena kita masih di dalam context Controller
                'maxStock' => $this->getAvailableStock($item->product_id, $outletId) + $item->qty
            ];
        });

        return view('poz::transaction.sale', [
            'action' => 'Perbarui',
            'outletId' => $outletId,
            'products' => $products,
            'sale' => $sale,
            'selectedItems' => $selectedItems
        ]);
    }

    public function store(Request $request)
    {
        $items = $request->items;
        $outletId = $request->outlet_id;

        if(empty($items)) return back()->with('msg-gagal', "Pilih minimal satu produk!");

        foreach ($items as $item) {
            $stock = $this->getAvailableStock($item['id'], $outletId);
            if ($item['qty'] > $stock) {
                return back()->with('msg-gagal', "Stok {$item['name']} tidak mencukupi!");
            }
        }

        $totals = $this->calculateSaleTotals($items, $request->discount);

        return redirect()->route('poz::transaction.sale.index')->with('msg-sukses', "Data berhasil disimpan");
    }

    public function destroy(Request $request)
    {
        $sale = SaleData::findOrFail($request->sale);
        $sale->delete();

        return redirect(route('poz::transaction.sale.index'))->with('msg-sukses', "Data berhasil dihapus");
    }

    public function invoice($sale_id)
    {
        $data['sale'] = SaleData::find($sale_id);
        return view('poz::transaction.invoice_pos', $data);
    }

    public function saleTable(Request $request)
    {
        $outletId = $request->outlet;
        $sale = SaleData::select('id','reference', 'sale_status', 'grand_total');
        $saledirect = DB::table('sale_directs')
            ->select('id', 'reference', 'sale_status', 'grand_total')
            ->whereNull('deleted_at');

        if (!empty($search = $request->search)) {
            $sale->where(function ($query) use ($search) {
                $query->where('reference', 'ILIKE', "%{$search}%");
            });
        }

        if (!empty($order = $request->filter)) {
            if ($order === 'new') {
                $sale->orderBy('id', 'desc');
            } elseif ($order === 'old') {
                $sale->orderBy('id', 'asc');
            }
        }

        $combined = $sale->unionAll($saledirect);
        $query = DB::query()->fromSub($combined, 'combined')->orderBy('id', 'desc');

        return Table::of($query)
            ->addIndexColumn()
            ->addColumn('sale_status', function ($row) {
                if ($row->sale_status == 3) {
                    return '<span class="badge text-bg-success">completed</span>';
                }
                return '<span class="badge text-bg-warning">Waiting</span>';
            })
            ->addColumn('grand_total', function ($row) {
                return 'Rp ' . number_format($row->grand_total, 2);
            })
            ->addColumn('action', function ($row) {
                // Gunakan helper route Anda untuk tombol action
                $template = '<div class="btn-group">';
                $template .= '<a href="'.route('poz::transaction.sale.pos-invoice', ['sale_id' => $row->id]).'" class="btn btn-sm btn-info"><i class="fa fa-print"></i></a>';
                $template .= '<a href="'.route('poz::transaction.sale.edit', ['sale' => $row->id]).'" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                $template .= '</div>';
                return $template;
            })
            ->rawColumns(['sale_status', 'action'])->make(true);
    }
}

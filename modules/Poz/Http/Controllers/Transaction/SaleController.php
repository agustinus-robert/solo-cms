<?php

namespace Modules\Poz\Http\Controllers\Transaction;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\ProductVariant;
use Modules\Poz\Models\Sale as SaleData;
use Modules\Poz\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\ProductStock;
use Modules\Poz\Traits\SaleTrait;

class SaleController extends Controller
{
    use SaleTrait;

    private function getActiveShift()
    {
        return CashRegister::where('status', 'open')
            ->where('casier_id', Auth::id())
            ->first();
    }

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
        $outletId = $request->query('outlet', Auth::user()->outlet_id);
        $cashRegister = $this->getActiveShift();

        $products = Product::with(['variant'])
            ->whereHas('outlets', fn($q) => $q->where('outlet_id', $outletId))
            ->get();

        $stocks = ProductStock::whereHas('outlets', fn($q) => $q->where('outlet_id', $outletId))
            ->select('variant_code', 'qty', 'status')
            ->get();

        return view('poz::transaction.sale', [
            'action' => 'Buat',
            'outletId' => $outletId,
            'products' => $products,
            'stocks' => $stocks,
            'sale' => null,
            'selectedItems' => [],
            'cashRegister' => $cashRegister
        ]);
    }

    public function edit(Request $request)
    {
        $sale = SaleData::with('saleItems.product')->findOrFail($request->sale);
        $outletId = $request->query('outlet', Auth::user()->outlet_id);

        $cashRegister = $this->getActiveShift();

        $products = Product::with(['variant'])
            ->whereHas('outlets', fn($q) => $q->where('outlet_id', $outletId))
            ->get();

        $stocks = ProductStock::whereHas('outlets', fn($q) => $q->where('outlet_id', $outletId))
            ->select('variant_code', 'qty', 'status')
            ->get();

        $selectedItems = $sale->saleItems->map(function($item) use ($outletId) {
            return [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'bought_variants' => [
                    [
                        'code' => $item->variant_code,
                        'name' => $item->variant_name ?? 'Default',
                        'qty' => $item->qty,
                        'price' => (int)$item->price,
                        'maxStock' => $this->getAvailableStock($item->product_id, $outletId) + $item->qty
                    ]
                ]
            ];
        });

        return view('poz::transaction.sale', [
            'action' => 'Perbarui',
            'outletId' => $outletId,
            'products' => $products,
            'stocks' => $stocks,
            'sale' => $sale,
            'selectedItems' => $selectedItems,
            'cashRegister' => $cashRegister
        ]);
    }

    public function store(Request $request)
    {
        $items = json_decode($request->items, true);
        $outletId = $request->outlet_id;

        try {
            DB::beginTransaction();

            $totals = $this->calculateSaleTotals($items, $request->discount);
            $sale = $this->createSaleTransaction($request, $totals);
            $this->storeSaleItems($sale, $items);
            $this->deductStock($items, $outletId, $sale->id);

            DB::commit();
            return redirect()->route('poz::transaction.sale.index')
                            ->with('msg-sukses', "Transaksi #" . $sale->reference . " Berhasil!");

        } catch (\Exception $e) {
            DB::rollBack();

            dd([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);

            return back()->with('msg-gagal', "Gagal simpan: " . $e->getMessage());
        }
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
                $template = '<div class="btn-group">';
                $template .= '<a href="'.route('poz::transaction.sale.pos-invoice', ['sale_id' => $row->id]).'" class="btn btn-sm btn-info"><i class="fa fa-print"></i></a>';
                $template .= '<a href="'.route('poz::transaction.sale.edit', ['sale' => $row->id]).'" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                $template .= '</div>';
                return $template;
            })
            ->rawColumns(['sale_status', 'action'])->make(true);
    }
}

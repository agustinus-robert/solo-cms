<?php

namespace Modules\Poz\Http\Controllers\Supplierz\Reporting;

use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables as Table;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Supplier;
use Illuminate\Http\Request;
use Modules\Poz\Models\Adjustment;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Purchase;
use Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\Sale;
use Modules\Poz\Models\SupplierSchedule;
use Carbon\Carbon;
use Modules\Poz\Models\SaleDirect;

class ProductReportingController extends Controller
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
            dbuilder_table('purchase', 'Nama Produk'),
            dbuilder_table('outlet', 'Outlet'),
            dbuilder_table('shift', 'Shift'),
            dbuilder_table('tanggal', 'Tanggal'),
            dbuilder_table('stock', 'Jumlah Pengembalian'),
            dbuilder_table('note', 'Catatan')
        ];

        $data['title'] = 'Laporan Shift Supplier';

        return view('poz::supplierz.reporting.product-reporting', $data);
    }

    public function getReportProducts(Request $request)
    {
        $outletId = $request->outlet;
        $shift = $request->shift;
        $user = auth()->user(); // Ambil objek user untuk cek role

        $today = $request->date
            ? \Carbon\Carbon::parse($request->date)->startOfDay()
            : \Carbon\Carbon::today();

        $product = ProductStock::with(['outlets', 'product.productStockAdjustItems', 'stockable'])
            ->whereIn('stockable_type', [
                \Modules\Poz\Models\Adjustment::class,
            ]);

        if (!$user->hasRole(['administrator', 'owner'])) {
            $supplier = Supplier::where('user_id', $user->id)->first();

            if ($supplier) {
                $product->where('supplier_id', $supplier->id);
            } else {
                $product->where('supplier_id', 0);
            }
        }

        if (!empty($request->report) && $request->report !== 'all') {
            if ($request->report === 'now') {
                $product->whereDate('created_at', $today);
            } elseif ($request->report === 'yesterday') {
                $yesterday = \Carbon\Carbon::yesterday();
                $product->whereDate('created_at', $yesterday);
            } elseif ($request->report === 'thisweek') {
                $product->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->report === 'thismonth') {
                $product->whereMonth('created_at', now()->month);
            } elseif ($request->report === 'thisyear') {
                $product->whereYear('created_at', now()->year);
            }
        } else {
            $product->whereDate('created_at', $today);
        }

        if (!empty($outletId)) {
            $product->whereHas('outlets', function($q) use ($outletId) {
                $q->where('outlets.id', $outletId);
            });
        }

        if (in_array($shift, ['morning', 'afternoon', 'evening'])) {
            $product->where('shift', $shift);
        }

        return Table::of($product)
            ->addIndexColumn()
            ->addColumn('image', function ($row) {
                if (!empty($row->product?->location) && !empty($row->product?->image_name)) {
                    $image = $row->product->location . '/' . $row->product->image_name;
                    return "<img width='50' height='50' src='" . asset('uploads/' . $image) . "' />";
                } else {
                    return "<img src='https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg' width='50' height='50' />";
                }
            })
            ->addColumn('purchase', fn($row) => $row->product->name ?? '-')
            ->addColumn('outlet', fn($row) => $row->outlets->first()->name ?? '-')
            ->addColumn('tanggal', fn($row) => $row->created_at?->translatedFormat('l, d M Y H:i') ?? '-')
            ->addColumn('shift', fn($row) => $row->shift ?? '-')
            ->addColumn('stock', function($row) use ($today, $request) {
                $items = $row->product?->productStockAdjustItems ?? collect();

                if (!empty($request->report) && $request->report !== 'all') {
                    $items = $items->filter(function($item) use ($request, $today) {
                        $itemDate = \Carbon\Carbon::parse($item->created_at);
                        if ($request->report === 'now') return $itemDate->isSameDay($today);
                        if ($request->report === 'yesterday') return $itemDate->isSameDay(\Carbon\Carbon::yesterday());
                        if ($request->report === 'thismonth') return $itemDate->month === now()->month;
                        if ($request->report === 'thisyear') return $itemDate->year === now()->year;
                        return true;
                    });
                } else {
                    $items = $items->filter(fn($item) => $item->created_at && \Carbon\Carbon::parse($item->created_at)->isSameDay($today));
                }

                return $items->where('status', 'minus')->sum('qty');
            })
            ->addColumn('note', fn($row) => $row->stockable?->note ?? '-')
            ->rawColumns(['image', 'purchase'])
            ->make(true);
    }
}

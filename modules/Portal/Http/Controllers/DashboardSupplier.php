<?php

namespace Modules\Portal\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Poz\Models\Supplier;
use Modules\Poz\Models\ProductStock;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Adjustment;
use Illuminate\Support\Facades\Auth;
use DB;

class DashboardSupplier extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['administrator', 'owner']);
        $supplier = Supplier::where('user_id', $user->id)->first();
        $supplierId = $supplier->id ?? 0;

        $stockQuery = ProductStock::query();
        if (!$isAdmin) {
            $stockQuery->where('supplier_id', $supplierId);
        }

        $data['stok_masuk'] = (clone $stockQuery)->where('status', 'plus')->whereMonth('created_at', now()->month)->sum('qty');
        $data['stok_keluar'] = (clone $stockQuery)->where('status', 'minus')->whereMonth('created_at', now()->month)->sum(DB::raw('ABS(qty)'));

        $data['total_items'] = (clone $stockQuery)->distinct('product_id')->count('product_id');

        $data['topProducts'] = (clone $stockQuery)
            ->select('product_id', DB::raw('SUM(ABS(qty)) as total_qty'))
            ->where('status', 'minus')
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        $data['recent_activities'] = (clone $stockQuery)->with(['product', 'outlets'])->latest()->take(5)->get();

        return view('portal::home-supplier', array_merge($data, ['user' => $user]));
    }
}

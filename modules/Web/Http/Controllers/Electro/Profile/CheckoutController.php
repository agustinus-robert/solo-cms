<?php

namespace Modules\Web\Http\Controllers\Electro\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Poz\Models\Sale;
use Modules\Account\Models\UserAddress;
use Modules\Poz\Models\SaleMidtransPayment;
use Modules\Web\Traits\CheckoutTrait;
use Modules\Web\Traits\MidtransTrait;
use Modules\Web\Traits\RajaOngkirTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Poz\Models\Product;

class CheckoutController extends Controller
{
    use CheckoutTrait, MidtransTrait, RajaOngkirTrait;

   public function index()
    {
        $canEdit = false;
        $user = Auth::user();
        $cart = $this->getActiveCart();

        if (!$cart || empty($cart->items)) {
            return redirect()->route('web::web.cart.detail')->with('error', 'Keranjang belanja kosong!');
        }

        $addresses = collect();
        $address = null;
        if ($user) {
            $addresses = UserAddress::where('user_id', $user->id)->get();
            $address = $addresses->where('is_main', 1)->first() ?? $addresses->first();
        }

        $provinces = $this->getRemoteProvinces();
        $totals = $this->calculateCheckoutTotals($cart->items, 0);

        $items = collect($cart->items)->map(function($item) {
            $product = Product::find($item['product_id']);
            $item['product_model'] = $product;
            return $item;
        });

        return view('web::electro.profile.checkout', compact('items', 'totals', 'address', 'addresses', 'user', 'provinces', 'canEdit'));
    }

    public function getCities(Request $request) {
        return response()->json($this->getRemoteCities($request->province_id));
    }

    public function getRemoteDistricts(Request $request)
    {
        return response()->json($this->getDistricts($request->city_id));
    }

    public function calculateShipping(Request $request) {
        $cart = $this->getActiveCart();
        $totalWeight = 5;
        foreach ($cart->items as $item) {
            $product = Product::find($item['product_id']);
            $totalWeight += ($product->weight ?? 1000) * $item['qty'];
        }

        $costs = $this->calculateShippingCost(
            $request->origin,
            $request->destination,
            $totalWeight,
            $request->courier
        );

        return response()->json($costs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id'     => 'required',
            'payment_method' => 'required',
            'address'        => 'required',
            'phone'          => 'required'
        ]);

        $cart = $this->getActiveCart();
        if (!$cart || empty($cart->items)) return back()->with('error', 'Keranjang kosong.');

        DB::beginTransaction();
        try {

            $firstItem = collect($cart->items)->first();
            $product = Product::with('outlets')->find($firstItem['product_id']);

            $outletId = $product->outlets->first()?->id;

            if (!$outletId) {
                throw new \Exception("Produk tidak memiliki data outlet yang valid.");
            }

            $totals = $this->calculateCheckoutTotals($cart->items, $request->discount ?? 0);
            $reference = 'WEB-' . strtoupper(uniqid());

            $sale = Sale::create([
                'reference'   => $reference,
                'customer_id' => Auth::id(),
                'sale_status' => 1,
                'sub_total'   => $totals['sub_total'],
                'discount'    => $totals['discount'],
                'grand_total' => $totals['grand_total'],
                'paid_amount' => 0,
                'payment_method' => $request->payment_method,
                'note'        => ($request->note ?? '-') . ' | Alamat: ' . $request->address . ' | Telp: ' . $request->phone
            ]);

            $sale->outlets()->sync([$outletId]);

            $this->storeCheckoutItems($sale, $cart->items);
            $this->deductStockFromCart($cart->items, $outletId, $sale->id);

            $res = $this->chargeCoreApi($sale, $cart->items, $totals, $request->payment_method);

            $vaNumber = null;
            if ($request->payment_method == 'mandiri') {
                $vaNumber = $res->bill_key ?? null;
            } else {
                $vaNumber = $res->va_numbers[0]->va_number ?? null;
            }

            SaleMidtransPayment::create([
                'sale_id'            => $sale->id,
                'order_id'           => $res->order_id,
                'transaction_id'     => $res->transaction_id ?? null,
                'payment_type'       => $res->payment_type,
                'va_number'          => $vaNumber,
                'pdf_url'            => $res->pdf_url ?? null,
                'transaction_status' => $res->transaction_status,
                'status_code'        => $res->status_code,
                'gross_amount'       => $res->gross_amount,
                'full_response'      => (array) $res,
                'expiry_time'        => isset($res->expiry_time) ? date('Y-m-d H:i:s', strtotime($res->expiry_time)) : null,
            ]);

            $cart->delete();
            DB::commit();

            return redirect()->route('web::area.finish.index', ['reference' => $reference]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error [' . Auth::id() . ']: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

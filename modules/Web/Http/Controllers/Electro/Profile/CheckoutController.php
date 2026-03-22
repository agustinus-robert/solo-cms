<?php

namespace Modules\Web\Http\Controllers\Electro\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Poz\Models\Sale;
use Modules\Account\Models\UserAddress;
use Modules\Web\Traits\CheckoutTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Poz\Models\Product;

class CheckoutController extends Controller
{
    use CheckoutTrait;

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

        $totals = $this->calculateCheckoutTotals($cart->items, 0);

        $items = collect($cart->items)->map(function($item) {
            $product = Product::find($item['product_id']);
            $item['product_model'] = $product;
            return $item;
        });

        return view('web::electro.profile.checkout', compact('items', 'totals', 'address', 'addresses', 'user', 'canEdit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required',
            'address'   => 'required',
            'phone'     => 'required'
        ]);

        $cart = $this->getActiveCart();

        if (!$cart) return back()->with('error', 'Data keranjang tidak ditemukan.');

        DB::beginTransaction();
        try {
            $totals = $this->calculateCheckoutTotals($cart->items, $request->discount ?? 0);

            $sale = Sale::create([
                'reference'   => 'WEB-' . strtoupper(uniqid()),
                'customer_id' => Auth::id(),
                'sale_status' => 3,
                'sub_total'   => $totals['sub_total'],
                'discount'    => $totals['discount'],
                'grand_total' => $totals['grand_total'],
                'paid_amount' => $totals['grand_total'],
                'pos'         => 0,
                'note'        => $request->note . ' | Alamat: ' . $request->address . ' | Telp: ' . $request->phone
            ]);

            $sale->outlets()->sync([$request->outlet_id]);

            $this->storeCheckoutItems($sale, $cart->items);

            $this->deductStockFromCart($cart->items, $request->outlet_id, $sale->id);

            $cart->delete();

            DB::commit();
            return redirect()->route('web.page', ['controller' => 'shop', 'method' => 'thanks'])
                             ->with('success', 'Checkout Berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}

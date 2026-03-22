<?php

namespace Modules\Web\Http\Controllers\Electro\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Web\Models\Whistlist;
use Modules\Poz\Models\Product;

use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $wishlist = Whistlist::where('user_id', $userId)->first();

        $productIds = $wishlist ? ($wishlist->items ?? []) : [];
        $products = Product::whereIn('id', $productIds)->get();
        $canEdit = false;

        return view('web::electro.profile.wishlist', compact('products', 'canEdit'));
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $productId = $request->product_id;
        $userId = Auth::id();

        $wishlist = Whistlist::firstOrCreate(
            ['user_id' => $userId],
            ['items' => [], 'created_by' => $userId]
        );

        $items = $wishlist->items ?? [];

        if (in_array($productId, $items)) {
            $items = array_values(array_diff($items, [$productId]));
            $action = 'removed';
        } else {
            $items[] = (int)$productId;
            $action = 'added';
        }

        $wishlist->update([
            'items' => $items,
            'updated_by' => $userId
        ]);

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'count'  => count($items),
            'message' => $action == 'added' ? 'Berhasil ditambah ke wishlist' : 'Dihapus dari wishlist'
        ]);
    }

    public function getWishlistCount()
    {
        $count = 0;
        if (auth()->check()) {
            $wishlist = Whistlist::where('user_id', auth()->id())->first();
            $count = is_array($wishlist->items) ? count($wishlist->items) : 0;
            $items = ($wishlist && is_array($wishlist->items)) ? $wishlist->items : [];
        }

        return response()->json([
            'status' => 'success',
            'count'  => $count,
            'items'  => $items
        ]);
    }

    public function renderCorner()
    {
        $canEdit = false;
        $count = 0;
        if (auth()->check()) {
            $wishlist = Whistlist::where('user_id', auth()->id())->first();
            $count = count($wishlist->items ?? []);
        }

        return view('web::components.chart-version.electro.whistlist-corner', compact('count', 'canEdit'))->render();
    }
}

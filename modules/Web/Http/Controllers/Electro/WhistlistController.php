<?php

namespace Modules\Web\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Poz\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $productId = $request->id;

        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];

        $wishlistRecord = Wishlist::firstOrNew($identifier);
        $items = $wishlistRecord->items ?? [];

        if (in_array($productId, $items)) {
            $items = array_values(array_diff($items, [$productId]));
            $status = 'removed';
        } else {
            $items[] = $productId;
            $status = 'added';
        }

        $wishlistRecord->items = $items;
        $wishlistRecord->save();

        return response()->json(['status' => $status]);
    }

    public function renderCorner()
    {
        $identifier = Auth::check() ? ['user_id' => Auth::id()] : ['session_id' => session()->getId()];
        $wishlistRecord = Wishlist::where($identifier)->first();

        $count = $wishlistRecord ? count($wishlistRecord->items ?? []) : 0;

        return view('web::components.chart-version.electro.whistlist-corner', compact('count'))->render();
    }
}

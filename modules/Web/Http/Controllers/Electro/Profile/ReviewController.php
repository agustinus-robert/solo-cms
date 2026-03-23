<?php

namespace Modules\Web\Http\Controllers\Electro\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Web\Models\Whistlist;
use Modules\Web\Traits\HasProductReview;
use Modules\Poz\Models\Product;

use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use HasProductReview;

    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        try {
            $review = $product->addReview($request->all());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Terima kasih! Ulasan Anda telah disimpan.',
                    'data'    => [
                        'name'        => $review->name,
                        'rating'      => $review->rating,
                        'description' => $review->description,
                        'date'        => $review->created_at->diffForHumans(),
                        'initial'     => strtoupper(substr($review->name, 0, 1)),
                        'color'       => '#' . substr(md5($review->name), 0, 6),
                    ]
                ]);
            }

            return back()->with('success', 'Terima kasih!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function getReviews($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = $product->reviews()->latest()->get()->map(function($review) {
            return [
                'name' => $review->name,
                'description' => $review->description,
                'rating' => $review->rating,
                'date' => $review->created_at->format('M d, Y'),
                'color' => '#' . substr(md5($review->name), 0, 6),
                'initial' => strtoupper(substr($review->name, 0, 1))
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }
}

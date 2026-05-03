<?php
namespace Modules\Tour\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Models\TourAvailability;
use Modules\Tour\Models\TourLabel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Berdasarkan Daerah (Location)
        $tour_by_locations = Tour::groupBy('location')
            ->select('location', DB::raw('count(*) as total'))
            ->pluck('total', 'location')
            ->toArray();

        // 2. Berdasarkan Range Harga (Base Price)
        $prices = Tour::get();
        $tour_by_prices = [
            'Under 500k' => $prices->where('base_price', '<', 500000)->count(),
            '500k - 1M'  => $prices->whereBetween('base_price', [500000, 1000000])->count(),
            'Above 1M'   => $prices->where('base_price', '>', 1000000)->count(),
        ];

        // 3. Berdasarkan Status Ketersediaan (Hari Ini)
        $today = now()->format('Y-m-d');
        $availabilities = TourAvailability::where('available_date', $today)->get();
        $tour_by_availabilities = [
            'Available' => $availabilities->where('is_available', true)->count(),
            'Sold Out'  => $availabilities->where('is_available', false)->count(),
            'No Schedule' => TourPackage::count() - $availabilities->count(),
        ];

        // 4. Berdasarkan Penggunaan Label (Master Label)
        $tour_by_labels = TourLabel::withCount('packages')
            ->pluck('packages_count', 'name')
            ->toArray();

        return view('tour::dashboard', compact(
            'tour_by_locations',
            'tour_by_prices',
            'tour_by_availabilities',
            'tour_by_labels'
        ));
    }
}

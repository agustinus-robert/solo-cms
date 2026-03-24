<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use Modules\Poz\Models\Product;
use Modules\Poz\Models\Category;
use Modules\Web\Http\Controllers\Controller;

class ShopController extends Controller{

    protected $themeConfig;
    protected $prefix;
    public function __construct() {
        parent::__construct();

        $configPath = base_path('modules/Web/Http/Controllers/Electro/Config.php');
        if (file_exists($configPath)) {
            $this->themeConfig = require $configPath;
        }
        $this->prefix = 'web::'.$this->themeConfig['caller'].'.shop';
    }

    public function index(Request $request){
        $categories = Category::withCount('products')->get();
        $query = Product::with(['category']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('q')) {
            $query->where('name', 'ILIKE', '%' . $request->q . '%');
        }

        if ($request->filled('max_price') && $request->max_price > 0) {
            $query->where('wholesale', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'low': $query->orderBy('wholesale', 'asc'); break;
            case 'high': $query->orderBy('wholesale', 'desc'); break;
            default: $query->latest(); break;
        }

        $products = $query->paginate(9)->withQueryString();
        $featured = Product::latest()->limit(3)->get();

        if ($request->ajax()) {
            return view($this->prefix.'.partials.product-list', [
                'products' => $products
            ])->render();
        }

        $allSections = [
            $this->prefix.'.section1-carousel' => [
                'order' => 1,
                'data'  => [
                   'carousel' => get_data_by_menu('1859690265115931', null, false),
                   'offers' => get_data_by_menu('1859690530369920', null, false),
                   'canEdit' => $this->canEdit
                ]
            ],
            $this->prefix.'.section2-services' => [
                'order' => 2,
                'data'  => [
                    'items' => get_data_by_menu('1859690724124090', null, false),
                    'canEdit' => $this->canEdit
                ]
            ],
            $this->prefix.'.section3-product-offer' => [
                'order' => 3,
                'data'  => [
                    'items' => get_data_by_menu('1859690864013928', null, false),
                    'canEdit' => $this->canEdit
                ]
            ],
            $this->prefix.'.section4-shop' => [
                'order' => 4,
                'data'  => [
                    'products'   => $query->paginate(9)->withQueryString(),
                    'categories' => $categories,
                    'featured'   => $featured,
                ]
            ],
        ];

        $this->setSections($allSections);

        return view($this->prefix.'.init', [
            'sections' => $this->getPageSections()
        ]);
    }

    public function show($id){
        $product = Product::find($id);

        return view($this->prefix.'.show', [
            'product' => $product
        ]);
    }
}

<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use Modules\Web\Http\Controllers\Controller;

class HomeController extends Controller{
    public function index(){

        $allSections = [
            'web::electro.home.section1-carousel' => [
                'order' => 1,
                'data'  => []
            ],
            'web::electro.home.section2-services' => [
                'order' => 2,
                'data'  => []
            ],
            'web::electro.home.section3-product-offer' => [
                'order' => 3,
                'data'  => []
            ],
            'web::electro.home.section4-product' => [
                'order' => 4,
                'data'  => []
            ],
            'web::electro.home.section5-banner' => [
                'order' => 5,
                'data'  => []
            ],
            'web::electro.home.section6-product-end' => [
                'order' => 6,
                'data'  => []
            ],
            'web::electro.home.section7-bestseller' => [
                'order' => 7,
                'data'  => []
            ],
        ];

        $this->setSections($allSections);

        return view('web::electro.home.init', [
            'sections' => $this->getPageSections()
        ]);
    }
}

<?php

namespace Modules\Web\Http\Controllers\Electro;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function detail($param)
    {
        if (is_numeric($param)) {
            return "Detail product by ID: ".$param;
        }

        return "Detail product by slug: ".$param;
    }
}

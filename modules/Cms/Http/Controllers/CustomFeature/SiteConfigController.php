<?php

namespace Modules\Cms\Http\Controllers\CustomFeature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cms\Models\CmsSiteConfig;
use DataTables;
use Session;
use Redirect;
use DB;

class SiteConfigController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('access', CmsSiteConfig::class);

        return view('cms::custom_feature.site_config.index');
    }
}

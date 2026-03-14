<?php

namespace Modules\Cms\Http\Controllers;

use Modules\Reference\Http\Controllers\Controller;
use Modules\Admin\Models\CmsPost;
use Modules\Admin\Models\CmsContact;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;

class DashboardCmsController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function index()
    {
        return view('cms::cms_dashboard');
    }
}

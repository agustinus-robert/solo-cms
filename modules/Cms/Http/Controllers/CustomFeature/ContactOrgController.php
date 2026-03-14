<?php

namespace Modules\Cms\Http\Controllers\CustomFeature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cms\Models\CmsContact;
use DataTables;
use Session;
use Redirect;
use DB;

class ContactOrgController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('access', CmsContact::class);
        $data['contact'] = CmsContact::count();

        return view('cms::custom_feature.contact_org.index', $data);
    }
}

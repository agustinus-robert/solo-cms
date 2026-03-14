<?php

namespace Modules\Cms\Http\Controllers\Configure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cms\Models\CmsMenuCategory;
use DataTables;
use Session;
use Redirect;
use DB;

class CategoryzationNameController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('access', CmsMenuCategory::class);

        return view('cms::configure.categoryzation_name.index');
    }

    public function create(Request $request)
    {
        $this->authorize('access', CmsMenuCategory::class);

        return view('cms::configure.categoryzation_name.index');
    }

    public function edit()
    {
        $this->authorize('access', CmsMenuCategory::class);

        return view('cms::configure.categoryzation_name.index');
    }

    public function destroy(Request $request)
    {
        $id = CmsMenuCategory::find($request->categoryzation_name);
        if ($id->delete() == true) {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg', "Data berhasil dihapus");
        } else {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg-gagal', "Data gagal dihapus");
        }
    }
}

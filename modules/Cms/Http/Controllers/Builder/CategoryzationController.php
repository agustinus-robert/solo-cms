<?php

namespace Modules\Cms\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cms\Models\CmsCategory;
use DataTables;
use Session;
use Redirect;
use DB;

class CategoryzationController extends Controller
{
    public function __construct()
    {
        foreach ($_COOKIE as $indextion => $valuetion) {
            if ($indextion != 'XSRF-TOKEN' && $indextion != 'laravel_session' && $indextion != 'k_status' && $indextion != 'spots' && $indextion != 'SESSION_COOKIE' && $indextion != 'k_language') {
                setcookie($indextion, FALSE, -1, '/');
            }
        }
    }

    public function index(Request $request)
    {
        $this->authorize('access', CmsCategory::class);

        return view(
            'cms::builder.categoryzation.index',
            ['cat_id' => $request->cat_id]
        );
    }

    public function create(Request $request)
    {
        $this->authorize('access', CmsCategory::class);

        return view(
            'cms::builder.categoryzation.index',
            ['cat_id' => $request->cat_id]
        );
    }

    public function edit(Request $request)
    {
        $this->authorize('access', CmsCategory::class);

        return view(
            'cms::builder.categoryzation.index',
            ['cat_id' => $request->cat_id]
        );
    }

    public function destroy(Request $request)
    {
        $menu = CmsCategory::find($request->category);
        if ($menu->delete() == true) {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg', "Data berhasil dihapus");
        } else {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg-gagal', "Data gagal dihapus");
        }
    }
}

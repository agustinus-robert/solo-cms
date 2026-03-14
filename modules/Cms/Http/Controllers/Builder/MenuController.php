<?php

namespace Modules\Cms\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Cms\Models\CmsMenu;
use DataTables;
use Session;
use Redirect;
use DB;

class MenuController extends Controller
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
        //$this->authorize('access', CmsMenu::class);
        //dd();
        //  return CmsMenu::get();
        return view('cms::builder.menu.index');
    }

    public function create(Request $request)
    {
        //$this->authorize('access', CmsMenu::class);

        return view('cms::builder.menu.index');
    }

    public function edit()
    {
        //$this->authorize('access', CmsMenu::class);

        return view('cms::builder.menu.index');
    }

    public function destroy(Request $request)
    {
        $menu = CmsMenu::find($request->menu);
        if ($menu->delete() == true) {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg', "Data berhasil disimpan");
        } else {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg-gagal', "Data gagal disimpan");
        }
    }
}

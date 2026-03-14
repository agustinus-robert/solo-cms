<?php

namespace Modules\Cms\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Cms\Models\CmsPost;
use Modules\Cms\Models\CmsPostVideo;
use DataTables;
use Session;
use Redirect;
use DB;

class PostingVideoController extends Controller
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
        // $this->authorize('access', Post::class);

        return view('cms::builder.posting_video.index', [
            'id_menu' => $request->id_menu,
            'post_id' => $request->post_id
        ]);
    }

    public function create(Request $request)
    {
        //$this->authorize('access', Post::class);

        return view('cms::builder.posting_video.index', [
            'id_menu' => $request->id_menu,
            'post_id' => $request->post_id
        ]);
    }

    public function edit(Request $request)
    {
        $this->authorize('access', CmsPost::class);

        return view('cms::builder.posting_video.index', [
            'id_menu' => $request->id_menu,
            'post_id' => $request->post_id
        ]);
    }

    public function destroy(Request $request)
    {
        $id = CmsPostVideo::find($request->posting_video);
        if ($id->delete() == true) {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg', "Data berhasil dihapus");
        } else {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg-gagal', "Data gagal dihapus");
        }
    }
}

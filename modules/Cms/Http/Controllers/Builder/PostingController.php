<?php

namespace Modules\Cms\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Cms\Models\CmsPost;
use Modules\Cms\Models\CmsMenu;
use Modules\Cms\Models\CmsPostSchedule;
use DataTables;
use Redirect;
use DB;
use Illuminate\Support\Facades\Session;

class PostingController extends Controller
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
       // $this->authorize('access', CmsPost::class);

        if (Session::get('posting')) {
            Session::forget('posting');
        }
        return view('cms::builder.posting.index', [
            'post_count' => CmsPost::count(),
            'id_menu' => $request->id_menu,
            'type' => CmsMenu::find($request->id_menu)->type,
            'create_status' => CmsMenu::find($request->id_menu)
        ]);
    }

    public function create(Request $request)
    {
     //   $this->authorize('access', CmsPost::class);

        return view('cms::builder.posting.index', [
            'post_count' => CmsPost::count(),
            'id_menu' => $request->id_menu
        ]);
    }

    public function edit(Request $request)
    {
      //  $this->authorize('access', CmsPost::class);

        return view('cms::builder.posting.index', [
            'post_count' => CmsPost::count(),
            'id_menu' => $request->id_menu
        ]);
    }

    public function destroy(Request $request)
    {
        $id = CmsPost::find($request->posting);
        if ($id->delete() == true) {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg', "Data berhasil dihapus");
        } else {
            return redirect(\Request::server('HTTP_REFERER'))->with('msg-gagal', "Data gagal dihapus");
        }
    }

    public function publish(Request $request)
    {
        $num = $request->getRequestUri();
        $id = (int) filter_var($num, FILTER_SANITIZE_NUMBER_INT);

        $update = [
            'status' => 2
        ];

        $stt = CmsPost::where('id', $id)->update($update);
        if ($stt == true) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false]);
        }
    }

    public function draft(Request $request)
    {
        $num = $request->getRequestUri();
        $id = (int) filter_var($num, FILTER_SANITIZE_NUMBER_INT);

        $update = [
            'status' => 3
        ];

        $stt = CmsPost::where('id', $id)->update($update);
        if ($stt == true) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false]);
        }
    }

    public function sch_date(Request $request)
    {
        $id = $request->id;
        $data['post_id'] = $id;
        $data['sch_post'] = CmsPostSchedule::where('post_id', $id)->latest('id')->first();

        return View('cms::builder.posting.schedule.index', $data);
    }

    public function post_sch(Request $request)
    {
        $data['post_id'] = $request->input('post_id');
        $data['schedule_on'] = $request->input('date');
        $data['timepicker'] = $request->input('time');
        $data['created_by'] = \Auth::user()->id;
        $data['updated_by'] = \Auth::user()->id;

        //DB::table('post')->where('id', $request->input('post_id'))->update(['status' => 2]);

        CmsPostSchedule::insert($data);
    }

    public function cancel_post_sch(Request $request)
    {
        $data['deleted_by'] = \Auth::user()->id;
        $data['deleted_at'] = date('Y-m-d H:i:s');


        CmsPostSchedule::where('id', $request->id)->update($data);

        $id = CmsPostSchedule::where('id', $request->id)->get()->first()->post_id;
        $data_post['status'] = CmsPost::where('id', $id)->get()->first()->status;
        //$data_post['status'] =

        // $data_post['status'] = 2;
        CmsPost::where('id', $id)->update($data_post);
    }
}

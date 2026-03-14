<?php

namespace Modules\Cms\Http\Controllers\Builder;

use Illuminate\Http\Request;
use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\Services\DataTable;

class DataTableBuilderController extends Controller
{

    protected $dataTable;

    public function index(Request $request)
    {
        if (app()->runningInConsole()) {
            return response('CLI mode detected, skip datatable.');
        }

        $class = $request->query('class');

        if ($class && is_string($class) && class_exists($class)) {
            $dataTable = new $class;
            return $dataTable->ajax();
        }

        return response()->json(['error' => 'Class tidak valid'], 400);
    }
}

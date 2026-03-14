<?php

namespace Modules\Cms\Http\Controllers\Configure;

use Illuminate\Http\Request;
use Modules\Reference\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class DataTableConfigureController extends Controller
{

    protected $dataTable;

    public function __construct(DataTables $dataTable)
    {
        $this->dataTable = $dataTable;
    }

    public function index()
    {
        return $this->dataTable->ajax();
    }
}

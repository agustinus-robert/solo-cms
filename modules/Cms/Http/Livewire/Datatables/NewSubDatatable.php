<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\NewSubDatatables;
use Livewire\Component;



class NewSubDatatable extends Component
{

    public function render(NewSubDatatables $newSubDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $newSubDatatables->render('cms::livewire.datatables.new-sub-datatable', $data);
    }
}

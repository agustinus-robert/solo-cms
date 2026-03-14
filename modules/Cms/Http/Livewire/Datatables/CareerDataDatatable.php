<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\CareerDataDatatables;
use Livewire\Component;



class CareerDataDatatable extends Component
{

    public function render(CareerDataDatatables $careerDataDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $careerDataDatatables->render('cms::livewire.datatables.career-data-datatable', $data);
    }
}

<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\CategoryDatatables;
use Livewire\Component;



class CategoryzationDatatable extends Component
{

    public function render(CategoryDatatables $categoryzationDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $categoryzationDatatables->render('cms::livewire.datatables.categoryzation-datatable', $data);
    }
}

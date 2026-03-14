<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\CategoryzationMenuDatatables;
use Livewire\Component;



class CategoryzationMenuDatatable extends Component
{

    public function render(CategoryzationMenuDatatables $categoryzationDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $categoryzationDatatables->render('cms::livewire.datatables.categoyzation-menu-datatable', $data);
    }
}

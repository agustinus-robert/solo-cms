<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\MenuDatatables;
use Livewire\Component;



class MenuDatatable extends Component
{

    public function render(MenuDatatables $menuDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $menuDatatables->render('cms::livewire.datatables.menu-datatable', $data);
    }
}

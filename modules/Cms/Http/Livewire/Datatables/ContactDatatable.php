<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\ContactDatatables;
use Livewire\Component;



class ContactDatatable extends Component
{

    public function render(ContactDatatables $contactDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $contactDatatables->render('cms::livewire.datatables.contact-datatable', $data);
    }
}

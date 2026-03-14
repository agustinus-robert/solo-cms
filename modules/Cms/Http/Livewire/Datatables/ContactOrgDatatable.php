<?php

namespace Modules\Cms\Http\Livewire\Datatables;

use Modules\Cms\DataTables\ContactOrgDatatables;
use Livewire\Component;



class ContactOrgDatatable extends Component
{

    public function render(ContactOrgDatatables $contactOrgDatatables)
    {
        $data['trash'] = (isset($_GET['trash']) ? $_GET['trash'] : 0);

        return $contactOrgDatatables->render('cms::livewire.datatables.contact-org-datatable', $data);
    }
}

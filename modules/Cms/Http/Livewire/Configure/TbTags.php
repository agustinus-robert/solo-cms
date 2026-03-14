<?php

namespace Modules\Admin\Http\Livewire\Cfg;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\CmsTags;
use DB;

class TbTags extends Component
{
    public function render()
    {
        $data_table = CmsTags::get()->toArray();

        return view('livewire.cfg.tb-tags', ['posts' => $data_table]);
    }
}

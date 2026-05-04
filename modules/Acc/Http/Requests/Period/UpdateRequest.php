<?php

namespace Modules\Acc\Http\Requests\Period;

use Modules\Acc\Http\Requests\Period\StoreRequest;

class UpdateRequest extends StoreRequest
{
     public function transform(): array
    {
        return [
            'name'       => $this->input('name'),
            'start_date' => $this->input('start_date'),
            'end_date'   => $this->input('end_date'),
            'is_closed'  => $this->input('is_closed'),
        ];
    }
}

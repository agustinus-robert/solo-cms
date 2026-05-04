<?php

namespace Modules\Tour\Http\Requests\Location;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Daerah/Lokasi',
        ];
    }

    public function transform(): array
    {
        return [
            'name' => $this->input('name'),
            'slug' => Str::slug($this->input('name')),
        ];
    }
}

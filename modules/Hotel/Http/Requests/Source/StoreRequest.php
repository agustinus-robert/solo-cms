<?php

namespace Modules\Hotel\Http\Requests\Source;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Aturan validasi untuk Sumber Reservasi.
     */
    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255|unique:hotel_ref_sources,name',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Kustomisasi nama atribut untuk pesan error.
     */
    public function attributes(): array
    {
        return [
            'name'            => 'Nama Sumber',
            'commission_rate' => 'Rate Komisi',
        ];
    }

    /**
     * Transformasi data sebelum masuk ke repository.
     */
    public function transform(): array
    {
        return [
            'name'            => $this->input('name'),
            'commission_rate' => $this->input('commission_rate'),
        ];
    }
}

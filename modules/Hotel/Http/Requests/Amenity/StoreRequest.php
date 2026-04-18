<?php

namespace Modules\Hotel\Http\Requests\Amenity;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Aturan validasi untuk registrasi tamu baru.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:hotel_guests,id_card_number',
            'icon'     => 'required|string|max:100',
        ];
    }

    /**
     * Kustomisasi nama atribut untuk pesan error.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'icon' => 'Icon',
        ];
    }

    /**
     * Transformasi data sebelum masuk ke repository.
     */
    public function transform(): array
    {
        return [
            'name' => $this->input('name'),
            'icon'     => $this->input('icon')
        ];
    }
}

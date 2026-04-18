<?php

namespace Modules\Hotel\Http\Requests\Guests;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Aturan validasi untuk registrasi tamu baru.
     */
    public function rules(): array
    {
        return [
            'id_card_number' => 'required|string|unique:hotel_guests,id_card_number',
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'nullable|string|max:100',
            'phone_number'   => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
        ];
    }

    /**
     * Kustomisasi nama atribut untuk pesan error.
     */
    public function attributes(): array
    {
        return [
            'id_card_number' => 'Nomor Identitas (NIK)',
            'first_name'     => 'Nama Depan',
            'last_name'      => 'Nama Belakang',
            'phone_number'   => 'Nomor Telepon',
            'email'          => 'Alamat Email',
        ];
    }

    /**
     * Transformasi data sebelum masuk ke repository.
     */
    public function transform(): array
    {
        return [
            'id_card_number' => $this->input('id_card_number'),
            'first_name'     => $this->input('first_name'),
            'last_name'      => $this->input('last_name'),
            'phone_number'   => $this->input('phone_number'),
            'email'          => $this->input('email'),
        ];
    }
}

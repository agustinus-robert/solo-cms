<?php

namespace Modules\Tour\Http\Requests\Booking;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Aturan validasi untuk pembuatan master tour.
     */
    public function rules(): array
    {
        return [
            'title'         => 'required|string|max:255',
            'location'      => 'required|string|max:100',
            'base_price'    => 'nullable|numeric|min:0',
            'opening_hours' => 'nullable|string|max:100',
            'overview'      => 'nullable|string',
            'label_ids'     => 'nullable|array',
            'label_ids.*'   => 'exists:tour_labels,id',
            'highlights'    => 'nullable|array',
        ];
    }

    /**
     * Kustomisasi nama atribut.
     */
    public function attributes(): array
    {
        return [
            'title'      => 'Nama Tour',
            'location'   => 'Lokasi',
            'base_price' => 'Harga Dasar',
            'label_ids'  => 'Fasilitas',
        ];
    }

    /**
     * Transformasi data sebelum diproses oleh Repository.
     */
    public function transform(): array
    {
        // Bersihkan highlights dari nilai null atau string kosong
        $highlights = collect($this->input('highlights', []))
            ->filter(fn($item) => !empty($item))
            ->values()
            ->toArray();

        return [
            'title'         => $this->input('title'),
            'location'      => $this->input('location'),
            'base_price'    => $this->input('base_price') ?? 0,
            'opening_hours' => $this->input('opening_hours'),
            'overview'      => $this->input('overview'),
            'highlights'    => $highlights, // Array yang sudah bersih
            'label_ids'     => $this->input('label_ids', []), // Untuk sync di repo
        ];
    }
}

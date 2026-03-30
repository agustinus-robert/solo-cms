<?php

namespace Modules\Finance\Http\Requests\Payroll\PPh;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->salary);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Map to float values.
     */
 
    public function mapFloat(array $items)
    {
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $result[$key] = $this->mapFloat($item);
            } else {
                // Periksa apakah ini field angka (bukan slip, ctg, atau name)
                if (!in_array($key, ['slip', 'ctg', 'name'])) {
                    
                    // LOGIKA PERBAIKAN:
                    // Jika string mengandung titik, kita cek apakah itu desimal (seperti 80.00)
                    // Ciri desimal: titiknya cuma satu DAN digit di belakang titik bukan berjumlah 3 (ribuan biasanya .000)
                    if (str_contains($item, '.')) {
                        $parts = explode('.', $item);
                        $lastPart = end($parts);

                        // Jika di belakang titik ada 3 digit, kemungkinan besar itu ribuan (format ID)
                        if (strlen($lastPart) === 3 && count($parts) > 1) {
                            $result[$key] = (float) str_replace('.', '', $item);
                        } else {
                            // Jika bukan 3 digit (misal .00, .75, .5), anggap itu desimal murni
                            $result[$key] = (float) $item;
                        }
                    } else {
                        // Jika tidak ada titik, langsung jadikan float
                        $result[$key] = (float) $item;
                    }
                    
                } else {
                    $result[$key] = $item;
                }
            }
        }
        return $result ?? [];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        return [
            ...$this->only('start_at', 'end_at', 'amount', 'description'),
            'components' => (array) $this->mapFloat($this->input('components'))
        ];
    }
}

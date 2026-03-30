<?php

namespace Modules\Finance\Http\Requests\Payroll\Calculation;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
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
    /**
 * Map to float values.
 */
    public function mapFloat(array $items)
    {
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $result[$key] = $this->mapFloat($item);
            } else {
                if ($key === 'amount') {
                    // 1. Cek apakah ini format ribuan Indonesia (misal: 1.000.000 atau 2.500)
                    // Kita anggap ribuan jika titik berada di posisi ribuan dan TIDAK ada koma desimal,
                    // ATAU jika angka ini nilainya sangat besar tanpa desimal yang jelas.
                    
                    // Cara paling simpel: Jika mengandung titik tapi tidak mengandung koma, 
                    // dan kita tahu ini kategori "Rupiah" (bukan Rekapitulasi), titik harus dihapus.
                    // Tapi karena di sini kita tidak tahu kategorinya, kita pakai logika jumlah digit:
                    
                    if (str_contains($item, '.')) {
                        $parts = explode('.', $item);
                        $lastPart = end($parts);
                        
                        // Jika bagian terakhir panjangnya 3 digit (misal .000), hampir pasti itu ribuan.
                        // Jika bagian terakhir panjangnya 1 atau 2 digit (misal .75 atau .7), itu desimal.
                        if (strlen($lastPart) === 3 && count($parts) > 1) {
                            $result[$key] = (float) str_replace('.', '', $item);
                        } else {
                            // Anggap desimal (9.75, 80.00, 10.5)
                            $result[$key] = (float) $item;
                        }
                    } else {
                        $result[$key] = (float) $item;
                    }
                } else {
                    $result[$key] = !in_array($key, ['slip', 'ctg', 'name']) 
                        ? (float) str_replace('.', '', $item) 
                        : $item;
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
            ...$this->only('name', 'start_at', 'end_at', 'amount', 'description'),
            'components' => (array) $this->mapFloat($this->input('components'))
        ];
    }
}

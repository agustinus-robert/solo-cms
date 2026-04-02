<?php

namespace Modules\Finance\Http\Requests\Payroll\PPh;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
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
                if (!in_array($key, ['slip', 'ctg', 'name'])) {

                    if (str_contains($item, '.')) {
                        $parts = explode('.', $item);
                        $lastPart = end($parts);

                        if (strlen($lastPart) === 3 && count($parts) > 1) {
                            $result[$key] = (float) str_replace('.', '', $item);
                        } else {
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

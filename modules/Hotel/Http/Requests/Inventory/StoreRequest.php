<?php

namespace Modules\Hotel\Http\Requests\Inventory;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\Hotel\Enums\InventoryTypeEnum;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'type'        => ['required', new Enum(InventoryTypeEnum::class)],
            'unit'        => 'required|string|max:50',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ];
    }

    public function transform(): array
    {
        return $this->validated();
    }
}

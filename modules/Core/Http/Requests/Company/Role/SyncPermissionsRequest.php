<?php

namespace Modules\Core\Http\Requests\Company\Role;

use App\Http\Requests\FormRequest;

class SyncPermissionsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'permissions.*' => 'nullable|exists:app_permissions,id'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'permissions.*' => 'hak akses'
        ];
    }
}

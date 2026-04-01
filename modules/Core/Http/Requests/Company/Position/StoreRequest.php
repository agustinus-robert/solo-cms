<?php

namespace Modules\Core\Http\Requests\Company\Position;

use Modules\Core\Models\CompanyPosition;
use Modules\Core\Models\CompanyDepartment;
use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'dept_id'              => 'required',
            'position_type_id'     => 'required', // Tambahkan ini
            'kd'                   => 'required|max:191',
            'name'                 => 'required|max:191',
            'description'          => 'nullable',
            'default_applied_role' => 'nullable', // Tambahkan ini
            'parents.*'            => 'nullable',
            'children.*'           => 'nullable',
            'level'                => 'required|numeric|min:0|max:10',
            'is_visible'           => 'boolean'
        ];
    }



    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'dept_id'           => 'departemen',
            'kd'                => 'kode jabatan',
			'name'              => 'nama jabatan',
			'description'       => 'deskripsi',
			'parents.*'         => 'atasan',
            'children.*'        => 'bawahan',
            'level'             => 'tingkat',
			'is_visible'        => 'visibilitas'
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        return $this->only([
            'dept_id', 'position_type_id', 'kd', 'name', 'description',
            'default_applied_role', 'parents', 'children', 'level', 'is_visible'
        ]);
    }
}

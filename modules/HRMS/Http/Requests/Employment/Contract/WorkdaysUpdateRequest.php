<?php

namespace Modules\HRMS\Http\Requests\Employment\Contract;

use App\Http\Requests\FormRequest;

class WorkdaysUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'worktimes_default.*.*.*'  => 'nullable|date_format:H:i',
            'worktimes_default.*.*.*'  => 'nullable|date_format:H:i'
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform ()
    {
        return $this->validated();
    }
}

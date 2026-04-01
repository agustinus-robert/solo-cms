<?php

namespace Modules\HRMS\Http\Requests\Service\Attendance\Schedule;

class UpdateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'dates.*.*.*'       => 'nullable|date_format:H:i',
            'workdays_count'    => 'required|numeric'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'dates.*'           => 'waktu',
            'dates.*.*'         => 'waktu',
            'dates.*.*.*'       => 'waktu',
            'workdays_count'    => 'hari efektif',
        ];
    }
}

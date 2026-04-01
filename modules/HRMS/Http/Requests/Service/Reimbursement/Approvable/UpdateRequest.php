<?php

namespace Modules\HRMS\Http\Requests\Service\Reimbursement\Approvable;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Http\Requests\FormRequest;
use Modules\Core\Enums\ApprovableResultEnum;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'result.*'            => ['required', new Enum(ApprovableResultEnum::class)],
            'reason.*'            => Rule::requiredIf(ApprovableResultEnum::tryFrom($this->input('result.' . $this->approvable->id))->reasonRequirement())
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'result.*'            => 'status',
            'reason.*'            => 'alasan'
        ];
    }

    /**
     * Transform request into expected output.
     */
    public function transform()
    {
        return [
            'result' => $this->input('result.' . $this->approvable->id),
            'reason' => $this->input('reason.' . $this->approvable->id)
        ];
    }
}

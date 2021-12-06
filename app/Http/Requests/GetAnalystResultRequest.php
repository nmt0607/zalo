<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class GetAnalystResultRequest extends CustomFormRequest
{
    /**
     * Handle failed validation for needed fields
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedFieldsValidation(Validator $validator)
    {
        // $failedRules = $validator->failed();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'days' => 'string|int|between:1,7320',
        ];
    }
}

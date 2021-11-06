<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class UpdateCommentRequest extends CustomFormRequest
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
            'id_com' => [
                'required',
                'integer',
            ],
            'comment' => [
                'required',
                'string',
            ],
        ];
    }
}

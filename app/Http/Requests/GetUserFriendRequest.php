<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class GetUserFriendRequest extends CustomFormRequest
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
            'index' => [
                'required',
                'integer',
            ],
            'count' => [
                'required',
                'integer',
            ],
        ];
    }
}

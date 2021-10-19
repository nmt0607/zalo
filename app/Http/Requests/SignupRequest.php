<?php

namespace App\Http\Requests;

use App\Exceptions\UserExistedException;
use Illuminate\Contracts\Validation\Validator;

class SignupRequest extends CustomFormRequest
{
    /**
     * Handle failed validation for needed fields
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedFieldsValidation(Validator $validator)
    {
        $failedRules = $validator->failed();

        $failedPhonenumberRules = $failedRules['phonenumber'];

        if (isset($failedPhonenumberRules['Unique'])) {
            throw new UserExistedException();
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phonenumber' => [
                'required',
                'digits:10',
                'starts_with:0',
                'unique:users,phonenumber',
            ],
            'password' => [
                'required',
                'alpha_num',
                'between:6,10',
                'different:phonenumber',
            ]
        ];
    }
}

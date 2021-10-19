<?php

namespace App\Http\Requests;

use App\Exceptions\InvalidParameterValueException;
use App\Exceptions\NotEnoughParameterException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class CustomFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        /**
         * @var $failedRules array of failed rules by field
         * [
         *     'field1' => [
         *         'Required' => [],
         *         // Other rules...
         *     ],
         *     'field2' => array of failed rules,
         *     ...
         * ]
         */
        $failedRules = $validator->failed();
        foreach ($failedRules as $failedRule) {
            if (array_key_exists('Required', $failedRule)) {
                throw new NotEnoughParameterException();
            }
        }

        $this->failedFieldsValidation($validator);

        throw new InvalidParameterValueException();
    }

    /**
     * Handle failed validation for any fields
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    abstract protected function failedFieldsValidation(Validator $validator);

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

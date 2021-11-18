<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class GetConversationRequest extends CustomFormRequest
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
            'partner_id' => [
                'integer',
                'required_without:conversation_id',
                'prohibits:conversation_id',
            ],
            'conversation_id' => [
                'integer',
                'required_without:partner_id',
                'prohibits:partner_id',
            ],
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

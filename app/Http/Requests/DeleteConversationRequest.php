<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class DeleteConversationRequest extends CustomFormRequest
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
            'partner_id' => 'string|required_without:conversation_id',
            'conversation_id' => 'string|required_without:partner_id'
        ];
    }
}

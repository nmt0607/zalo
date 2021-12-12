<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class SetUserInfoRequest extends CustomFormRequest
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
            'user_name' => 'string|max:30',
            'description' => 'string|max:150',
            'avatar' => 'mimes:jpg,png,bmp,svg,webp|max:4096',
            'address' => 'string|max:100',
            'city' => 'string|max:30',
            'country' => 'string|max:30',
            'cover_image' => 'mimes:jpg,png,bmp,svg,webp|max:4096',
            'link' => 'string|max:100',
        ];
    }
}

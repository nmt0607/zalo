<?php

namespace App\Http\Requests;

use App\Exceptions\FileTooBigException;
use App\Exceptions\MaximumNumberOfImagesException;
use App\Rules\VideoDuration;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class GetCommentRequest extends CustomFormRequest
{
    /**
     * Handle failed validation for needed fields
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedFieldsValidation(Validator $validator)
    {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'id' => [
                'required',
            ],
            'index' => [
                'required',
            ],
            'count' => [
                'required',
            ],
        ];
    }
}

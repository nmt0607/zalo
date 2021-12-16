<?php

namespace App\Http\Requests;

use App\Exceptions\FileTooBigException;
use App\Exceptions\MaximumNumberOfImagesException;
use App\Rules\VideoDuration;
use Illuminate\Contracts\Validation\Validator;

class CreatePostRequest extends CustomFormRequest
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

        foreach ($failedRules as $field => $failedRulesForField) {
            // Handle max number of images failed
            if ($field === 'image' && isset($failedRulesForField['max'])) {
                throw new MaximumNumberOfImagesException();
            }

            // Handle max video size and max image size failed
            if (
                ($field === 'video' || preg_match('/^image\.[0-3]$/', $field))
                && isset($failedRulesForField['max'])
            ) {
                throw new FileTooBigException();
            }
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

            'described' => [
                'string',
                'max:500',
            ],
            'image' => [
                'array',
                'max:4',
                'prohibits:video',
            ],
            'image.*' => [
                'mimes:jpg,png,bmp,webp',
                'max:4096', // 4MB
            ],
            'video' => [
                //'mimetypes:video/*',
                'max:10240', // 10MB
                //new VideoDuration(),
                'prohibits:image',
            ],
        ];
    }
}

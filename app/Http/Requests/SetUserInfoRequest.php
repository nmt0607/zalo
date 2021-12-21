<?php

namespace App\Http\Requests;

use App\Exceptions\FileTooBigException;
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
        $failedRules = $validator->failed();

        if (isset($failedRules['avatar']['Max']) || isset($failedRules['cover_image']['Max']))
            throw new FileTooBigException();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name' => 'string|regex:/^[a-z0-9A-Z\s\-_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ]{6,30}$/u',
            'description' => 'string|max:150',
            'avatar' => 'mimes:jpg,png,bmp,svg,webp|max:4096',
            'address' => 'string|max:100',
            'country' => 'string|max:100',
            'cover_image' => 'mimes:jpg,png,bmp,svg,webp|max:4096',
            'link' => 'string|max:100',
        ];
    }
}

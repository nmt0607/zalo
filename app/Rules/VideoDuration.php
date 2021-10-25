<?php

namespace App\Rules;

use getID3;
use Illuminate\Contracts\Validation\Rule;

class VideoDuration implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $videoInfo = (new getID3())->analyze($value->getPathname());
        $videoDuration = $videoInfo['playtime_seconds'];

        if ($videoDuration >= 1 && $videoDuration <= 10) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

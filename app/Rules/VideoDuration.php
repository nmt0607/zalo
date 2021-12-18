<?php

namespace App\Rules;

use App\Exceptions\FileTooBigException;
use App\Exceptions\InvalidParameterValueException;
use App\Exceptions\VideoDurationException;
use Exception;
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
        try {
            $videoInfo = (new getID3())->analyze($value->getPathname());
            $videoDuration = $videoInfo['playtime_seconds'];

            if ($videoDuration >= 1 && $videoDuration <= 10) {
                return true;
            }
        }
        catch(Exception $e) {
            throw new InvalidParameterValueException();
        }

        throw new VideoDurationException();
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

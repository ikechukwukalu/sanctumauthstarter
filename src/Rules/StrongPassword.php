<?php

namespace Ikechukwukalu\Sanctumauthstarter\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
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
        return preg_match('^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[special characters]).{8,16}$^', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('sanctumauthstarter::passwords.strength');
    }
}

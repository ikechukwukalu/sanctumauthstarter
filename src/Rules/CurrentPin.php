<?php

namespace Ikechukwukalu\Sanctumauthstarter\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CurrentPin implements Rule
{

    private bool $defaultPin = false;
    private bool $allowDefaultPin = false;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(bool $allowDefaultPin = false)
    {
        //
        $this->allowDefaultPin = $allowDefaultPin;
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
        if (Auth::user()->default_pin && !$this->allowDefaultPin) {
            $this->defaultPin = true;

            return false;
        }

        return Hash::check($value, Auth::user()->pin);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->defaultPin) {
            return trans('sanctumauthstarter::pin.default');
        }

        return trans('sanctumauthstarter::pin.wrong');
    }
}

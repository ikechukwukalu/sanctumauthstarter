<?php

namespace Ikechukwukalu\Sanctumauthstarter\Rules;

use Illuminate\Contracts\Validation\Rule;
use Ikechukwukalu\Sanctumauthstarter\Models\OldPin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DisallowOldPin implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private int|bool $checkAll;
    private int $number;

    public function __construct($checkAll = true, $number = 4)
    {
        //
        $this->checkAll = $checkAll;
        $this->number = $number;

        if (is_int($this->checkAll) && !empty($this->checkAll)) {
            $this->number = $checkAll;
        }
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
        $user = Auth::user();

        if ($this->checkAll === true) {
            $oldpins = OldPin::whereBelongsTo($user)
                                ->orderBy('created_at', 'desc')
                                ->get();
        } else {
            $oldpins = OldPin::whereBelongsTo($user)
                                ->orderBy('created_at', 'desc')
                                ->take($this->number)
                                ->get();
        }

        if ($oldpins->count() === 0) {
            return !Hash::check($value, $user->pin);
        }

        foreach ($oldpins as $oldpin) {
            if (Hash::check($value, $oldpin->pin)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans_choice('sanctumauthstarter::pin.exists', intval(is_int($this->checkAll)), ['number' => $this->number]);
    }
}

<?php

namespace Ikechukwukalu\Sanctumauthstarter\Rules;

use Ikechukwukalu\Sanctumauthstarter\Models\OldPassword;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DisallowOldPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private int|bool $checkAll;
    private int $number;
    private $user;

    public function __construct($checkAll = true, $number = 4)
    {
        //
        $this->checkAll = $checkAll;
        $this->number = $number;

        if (is_int($this->checkAll) && !empty($this->checkAll)) {
            $this->number = $checkAll;
        }

        $this->user = Auth::user();
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
        $oldpasswords = $this->getOldPasswords();

        if ($oldpasswords->count() === 0) {
            return !Hash::check($value, $this->user->password);
        }

        foreach ($oldpasswords as $oldpassword) {
            if (Hash::check($value, $oldpassword->password)) {
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
        return trans_choice('sanctumauthstarter::passwords.exists', intval(is_int($this->checkAll)), ['number' => $this->number]);
    }

    /**
     * Get OldPin Model.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getOldPasswords(): EloquentCollection
    {
        if ($this->checkAll === true) {
            return OldPassword::where('user_id', $this->user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        }

        return OldPassword::where('user_id', $this->user->id)
                ->orderBy('created_at', 'desc')
                ->take($this->number)
                ->get();
    }
}

<?php

namespace Ikechukwukalu\Sanctumauthstarter\Listeners;

use Ikechukwukalu\Sanctumauthstarter\Events\ForgotPassword;
use Illuminate\Support\Facades\Password;

class SendResetLink extends UserEventListener
{
    private ForgotPassword $event;

    public function handle($event)
    {
        $this->event = $event;
        Password::sendResetLink(['email' => $this->event->user->email]);
    }
}

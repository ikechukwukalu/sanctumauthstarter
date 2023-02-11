<?php

namespace Ikechukwukalu\Sanctumauthstarter\Listeners;

use Ikechukwukalu\Sanctumauthstarter\Events\EmailVerification;

class SendEmailVerificationNotification extends UserEventListener
{
    private EmailVerification $event;

    public function handle($event)
    {
        $this->event = $event;
        $this->event->user->sendEmailVerificationNotification();
    }
}

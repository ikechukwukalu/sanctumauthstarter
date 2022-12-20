<?php

namespace Ikechukwukalu\Sanctumauthstarter\Listeners;

use Ikechukwukalu\Sanctumauthstarter\Events\EmailVerification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailVerificationNotification implements ShouldQueue
{
    public $queue = 'high';
    public $tries = 5;

    public function viaConnection()
    {
        return env('QUEUE_CONNECTION', 'redis');
    }

    public function handle(EmailVerification $event)
    {
        $event->user->sendEmailVerificationNotification();
    }
}

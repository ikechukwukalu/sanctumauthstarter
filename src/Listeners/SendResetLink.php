<?php

namespace Ikechukwukalu\Sanctumauthstarter\Listeners;

use Ikechukwukalu\Sanctumauthstarter\Events\ForgotPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Password;

class SendResetLink implements ShouldQueue
{
    public $queue = 'high';
    public $tries = 5;

    public function viaConnection()
    {
        return env('QUEUE_CONNECTION', 'redis');
    }

    public function handle(ForgotPassword $event)
    {
        Password::sendResetLink(['email' => $event->user->email]);
    }
}

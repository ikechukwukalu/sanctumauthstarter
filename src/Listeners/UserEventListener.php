<?php

namespace Ikechukwukalu\Sanctumauthstarter\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserEventListener implements ShouldQueue
{
    public $queue = 'high';
    public $tries = 5;

    public function viaConnection()
    {
        return env('QUEUE_CONNECTION', 'redis');
    }

    public function handle($event)
    {
        //
    }
}

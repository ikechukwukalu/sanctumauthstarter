<?php

namespace Ikechukwukalu\Sanctumauthstarter\Events;

use Illuminate\Broadcasting\Channel;

class TwoFactorLogin extends WebViewLogin
{
    public function broadcastOn()
    {
        return new Channel('access.token.twofactor.' . $this->userUUID);
    }
}

<?php

namespace Ikechukwukalu\Sanctumauthstarter\Events;

use Illuminate\Broadcasting\Channel;

class SocialiteLogin extends WebViewLogin
{
    public function broadcastOn()
    {
        return new Channel('access.token.socialite.' . $this->userUUID);
    }
}

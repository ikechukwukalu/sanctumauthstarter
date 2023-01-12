<?php

namespace Ikechukwukalu\Sanctumauthstarter\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class SocialiteLogin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public NewAccessToken $accessToken;
    public string $userUUID;

    public function __construct(User $user, NewAccessToken $accessToken, string $userUUID)
    {
        $this->user = $user;
        $this->accessToken = $accessToken;
        $this->userUUID = $userUUID;
    }

    public function broadcastOn()
    {
        return new Channel('access.token.' . $this->userUUID);
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'access_token' => $this->accessToken->plainTextToken
        ];
    }
}

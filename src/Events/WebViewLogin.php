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

class WebViewLogin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected User $user;
    protected NewAccessToken $accessToken;
    protected string $userUUID;
    protected ?array $data;

    public function __construct(User $user, NewAccessToken $accessToken,
                            string $userUUID, ?array $data = null)
    {
        $this->user = $user;
        $this->accessToken = $accessToken;
        $this->userUUID = $userUUID;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('access.token.' . $this->userUUID);
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'access_token' => $this->accessToken->plainTextToken,
            'data' => $this->data,
        ];
    }
}

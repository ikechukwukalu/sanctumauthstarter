<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Ikechukwukalu\Sanctumauthstarter\Models\TestUser;
use App\Models\User;

class WelcomeUser extends UserNotification
{
    private TestUser|User $user;

    public function __construct(TestUser|User $user)
    {
        $this->user = $user;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(trans('sanctumauthstarter::notify.welcome.subject', ['name' => $this->user->name]))
                    ->line(trans('sanctumauthstarter::notify.welcome.introduction', ['name' => $this->user->name]))
                    ->line(trans('sanctumauthstarter::notify.welcome.message'))
                    ->action(trans('sanctumauthstarter::notify.welcome.action'), url('/'))
                    ->line(trans('sanctumauthstarter::notify.welcome.complimentary_close'));
    }
}

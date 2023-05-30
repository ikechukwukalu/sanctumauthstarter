<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class UserLogin extends UserNotification
{
    private string $time;
    private string $deviceAndLocation;

    public function __construct(string $time, array $deviceAndLocation)
    {
        $this->time = $time;
        $this->deviceAndLocation = implode(", ", $deviceAndLocation);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.login.subject'))
            ->line(trans('sanctumauthstarter::notify.login.introduction', ['time' => $this->time, 'deviceAndLocation' => $this->deviceAndLocation]))
            ->line(trans('sanctumauthstarter::notify.login.message'))
            ->action(trans('sanctumauthstarter::notify.login.action'), route(config('sanctumauthstarter.notification_url.login.user_login')))
            ->line(trans('sanctumauthstarter::notify.login.complimentary_close'));
    }
}

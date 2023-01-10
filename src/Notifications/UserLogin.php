<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserLogin extends Notification implements ShouldQueue
{
    use Queueable;

    private string $time;
    private string $deviceAndLocation;

    public function __construct(string $time, array $deviceAndLocation)
    {
        $this->time = $time;
        $this->deviceAndLocation = implode(", ", $deviceAndLocation);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.login.subject'))
            ->line(trans('sanctumauthstarter::notify.login.introduction', ['time' => $this->time, 'deviceAndLocation' => $this->deviceAndLocation]))
            ->line(trans('sanctumauthstarter::notify.login.message'))
            ->action(trans('sanctumauthstarter::notify.login.action'), route('password.reset'))
            ->line(trans('sanctumauthstarter::notify.login.complimentary_close'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

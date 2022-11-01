<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PinChange extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.pin.subject'))
            ->line(trans('sanctumauthstarter::notify.pin.introduction'))
            ->line(trans('sanctumauthstarter::notify.pin.message'))
            ->action(trans('sanctumauthstarter::notify.pin.action'), route('changePin'))
            ->line(trans('sanctumauthstarter::notify.pin.complimentary_close'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

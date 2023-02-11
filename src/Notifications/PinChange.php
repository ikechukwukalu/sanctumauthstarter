<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PinChange extends UserNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.pin.subject'))
            ->line(trans('sanctumauthstarter::notify.pin.introduction'))
            ->line(trans('sanctumauthstarter::notify.pin.message'))
            ->action(trans('sanctumauthstarter::notify.pin.action'), route('changePin'))
            ->line(trans('sanctumauthstarter::notify.pin.complimentary_close'));
    }
}

<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PasswordChange extends UserNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.password.subject'))
            ->line(trans('sanctumauthstarter::notify.password.introduction'))
            ->line(trans('sanctumauthstarter::notify.password.message'))
            ->action(trans('sanctumauthstarter::notify.password.action'), route('password.reset'))
            ->line(trans('sanctumauthstarter::notify.password.complimentary_close'));
    }
}

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
            ->action(trans('sanctumauthstarter::notify.password.action'), route(config('sanctumauthstarter.notification_url.password.password_change')))
            ->line(trans('sanctumauthstarter::notify.password.complimentary_close'));
    }
}

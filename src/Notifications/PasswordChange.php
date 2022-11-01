<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChange extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.password.subject'))
            ->line(trans('sanctumauthstarter::notify.password.introduction'))
            ->line(trans('sanctumauthstarter::notify.password.message'))
            ->action(trans('sanctumauthstarter::notify.password.action'), route('changePassword'))
            ->line(trans('sanctumauthstarter::notify.password.complimentary_close'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

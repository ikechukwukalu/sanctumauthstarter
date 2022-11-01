<?php

namespace Ikechukwukalu\Sanctumauthstarter\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class WelcomeUser extends Notification implements ShouldQueue
{
    use Queueable;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
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

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

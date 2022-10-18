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
    private string $device;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $time, array $device)
    {
        //
        $this->time = $time;
        $this->device = implode(", ", $device);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sanctumauthstarter::notify.login.subject'))
            ->line(trans('sanctumauthstarter::notify.login.introduction', ['time' => $this->time, 'device' => $this->device]))
            ->line(trans('sanctumauthstarter::notify.pin.message'))
            ->action(trans('sanctumauthstarter::notify.pin.action'), route('changePassword'))
            ->line(trans('sanctumauthstarter::notify.login.complimentary_close'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

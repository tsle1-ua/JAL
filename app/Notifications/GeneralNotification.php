<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $title, public string $body)
    {
    }

    public function via(object $notifiable): array
    {
        return ['webpush'];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body);
    }
}

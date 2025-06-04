<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewEventInFollowedCategoryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Event $event)
    {
    }

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\FcmChannel::class];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => 'Nuevo evento en ' . ($this->event->category?->name ?? ''),
                'body' => $this->event->title,
            ],
            'data' => [
                'event_id' => $this->event->id,
            ],
        ];
    }
}

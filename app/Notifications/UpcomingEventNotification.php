<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Próximo evento: ' . $this->event->title)
            ->line('El evento "' . $this->event->title . '" tendrá lugar pronto.')
            ->action('Ver detalles', url('/events/' . $this->event->id))
            ->line('Gracias por usar nuestra plataforma.');
    }
}

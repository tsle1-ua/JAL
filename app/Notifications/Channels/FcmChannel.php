<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class FcmChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $token = $notifiable->fcm_token;
        if (!$token) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        $serverKey = config('services.fcm.server_key');
        if (!$serverKey) {
            return;
        }

        Http::withToken($serverKey)
            ->post('https://fcm.googleapis.com/fcm/send', array_merge(['to' => $token], $payload));
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyEventAttendees extends Command
{
    protected $signature = 'events:notify-attendees';

    protected $description = 'Notify attendees 24 hours before the event starts';

    public function handle(): int
    {
        $targetDate = now()->addDay()->toDateString();
        $events = Event::with('attendees')
            ->whereDate('date', $targetDate)
            ->get();

        foreach ($events as $event) {
            foreach ($event->attendees as $user) {
                $user->notify(new EventReminderNotification($event));
            }
        }

        $this->info('Event reminder notifications sent.');
        return Command::SUCCESS;
    }
}

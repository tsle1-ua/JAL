<?php

namespace App\Console;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\NotifyEventAttendees;
use App\Models\Event;
use App\Notifications\UpcomingEventNotification;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('events:notify-attendees')->daily();
        $schedule->command('listings:cleanup')->daily();
        $schedule->call(function () {
            $events = Event::with('attendees')
                ->whereDate('date', Carbon::tomorrow()->toDateString())
                ->get();

            foreach ($events as $event) {
                foreach ($event->attendees as $user) {
                    $user->notify(new UpcomingEventNotification($event));
                }
            }
        })->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\NotifyEventAttendees;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('events:notify-attendees')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

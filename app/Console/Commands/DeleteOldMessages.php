<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Carbon\Carbon;

class DeleteOldMessages extends Command
{
    protected $signature = 'messages:delete-old';

    protected $description = 'Delete messages older than configured period';

    public function handle(): int
    {
        $days = config('messages.retention_days', 30);
        $cutoffDate = Carbon::now()->subDays($days);

        $deleted = Message::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deleted} messages older than {$days} days.");

        return Command::SUCCESS;
    }
}

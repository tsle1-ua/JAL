<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupListings extends Command
{
    protected $signature = 'listings:cleanup';

    protected $description = 'Remove listings past the available_from threshold';

    public function handle(): int
    {
        $days = config('listings.cleanup_threshold_days', 60);
        $cutoffDate = now()->subDays($days)->startOfDay();

        $listings = Listing::whereDate('available_from', '<', $cutoffDate)->get();
        $count = $listings->count();

        foreach ($listings as $listing) {
            foreach ($listing->image_paths ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }
            $listing->delete();
        }

        $this->info("{$count} listings cleaned up.");

        return Command::SUCCESS;
    }
}

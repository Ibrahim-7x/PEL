<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mention;
use Illuminate\Support\Facades\Log;

class ExpireOldMentions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentions:expire-old {--days=1 : Number of days after which to expire mentions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old mention notifications that are older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        $this->info("Expiring mentions older than {$days} day(s)...");

        // Delete mentions that are older than the specified days and are read
        $expiredMentions = Mention::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();

        $this->info("Expired {$expiredMentions} old mention(s).");

        Log::info('Expired old mentions', [
            'expired_count' => $expiredMentions,
            'days_threshold' => $days
        ]);

        return Command::SUCCESS;
    }
}

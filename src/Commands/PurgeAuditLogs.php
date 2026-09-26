<?php

namespace Zhenjun\AuditTrail\Commands;

use Illuminate\Console\Command;
use Zhenjun\AuditTrail\Models\AuditLog;

class PurgeAuditLogs extends Command
{
    protected $signature = 'audit:purge
        {--days= : Number of days to keep logs for. Defaults to the config value.}';

    protected $description = 'Delete audit logs older than the given number of days';

    public function handle(): int
    {
        $daysOption = $this->option('days');

        $days = ($daysOption === null || $daysOption === '')
            ? (int) config('filament-audit-trail.purge_days', 365)
            : (int) $daysOption;

        if ($days < 1) {
            $this->error('The number of days must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);

        $deleted = AuditLog::query()->where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} audit log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}

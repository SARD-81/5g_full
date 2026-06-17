<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Log\Services\ActivityLogFileExporterService;

class ExportActivityLogsToFile extends Command
{
    protected $signature = 'logs:export-activity-to-file';

    protected $description = 'Export new activity logs to daily rotating .log files (runs every 5 minutes)';

    public function handle(ActivityLogFileExporterService $exporter): int
    {
        $this->info('Starting activity log file export...');

        try {
            $exportedCount = $exporter->export();

            if ($exportedCount > 0) {
                $this->info("Successfully exported {$exportedCount} new log(s) to file.");
            } else {
                $this->info('No new logs to export at this time.');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error during log export: ' . $e->getMessage());
            report($e);
            return self::FAILURE;
        }
    }
}

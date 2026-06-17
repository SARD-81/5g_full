<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Log\Services\ActivityLogFileExporterService;

class ExportActivityLogsToFile extends Command
{
    protected $signature = 'logs:export-activity-to-file';

    protected $description = 'Export activity logs to daily .log files with rotation (every 5 minutes)';

    public function handle(ActivityLogFileExporterService $exporter): int
    {
        $this->info('Starting activity log export to file...');

        try {
            $exporter->export();
            $this->info('Activity logs successfully exported to file.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error exporting logs: ' . $e->getMessage());
            report($e);
            return self::FAILURE;
        }
    }
}

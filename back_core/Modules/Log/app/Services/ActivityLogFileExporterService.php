<?php

namespace Modules\Log\Services;

use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogFileExporterService
{
    protected string $logDirectory = 'logs/5g-activity-logs';
    protected int $retentionDays = 30;

    public function export(): int
    {
        $fullPath = storage_path('app/' . $this->logDirectory);

        // اطمینان از وجود دایرکتوری
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $lastExportedId = $this->getLastExportedId();

        $query = Activity::query()->orderBy('id', 'asc');

        if ($lastExportedId) {
            $query->where('id', '>', $lastExportedId);
        }

        $newLogs = $query->get();
        $count = $newLogs->count();

        if ($count === 0) {
            return 0;
        }

        $formattedLogs = $this->formatLogs($newLogs);

        // نوشتن مستقیم با file_put_contents (قابل اعتمادتر)
        $filename = $this->getCurrentDayFilename();
        file_put_contents(
            $fullPath . '/' . $filename,
            $formattedLogs,
            FILE_APPEND | LOCK_EX
        );

        $this->updateLastExportedId($newLogs->last()->id);
        $this->rotateAndCleanOldLogs();

        return $count;
    }

    protected function formatLogs($logs): string
    {
        $output = '';

        foreach ($logs as $log) {
            $timestamp = $log->created_at->format('Y-m-d H:i:s');
            $level = strtoupper($log->log_name ?? 'INFO');
            $event = $log->event ?? 'unknown';
            $description = $log->description ?? '';

            $properties = $log->properties ?? [];
            $extra = $this->extractExtraInfo($properties);

            $line = sprintf(
                "[%s] %-8s %-20s | %s%s\n",
                $timestamp,
                $level,
                $event,
                $description,
                $extra ? ' | ' . $extra : ''
            );

            $output .= $line;
        }

        return $output;
    }

    protected function extractExtraInfo($properties): string
    {
        $parts = [];

        if (isset($properties['user_id'])) $parts[] = 'user_id=' . $properties['user_id'];
        if (isset($properties['ip']))       $parts[] = 'ip=' . $properties['ip'];
        if (isset($properties['server_id'])) $parts[] = 'server_id=' . $properties['server_id'];
        if (isset($properties['module_id'])) $parts[] = 'module_id=' . $properties['module_id'];

        return implode(' | ', $parts);
    }

    protected function getCurrentDayFilename(): string
    {
        return 'activity-' . now()->format('Y-m-d') . '.log';
    }

    protected function rotateAndCleanOldLogs(): void
    {
        $fullPath = storage_path('app/' . $this->logDirectory);

        if (!is_dir($fullPath)) return;

        $files = scandir($fullPath);
        $cutoffDate = now()->subDays($this->retentionDays);

        foreach ($files as $file) {
            if (!str_ends_with($file, '.log')) continue;

            if (preg_match('/activity-(\d{4}-\d{2}-\d{2})\.log/', $file, $matches)) {
                $fileDate = Carbon::createFromFormat('Y-m-d', $matches[1]);
                if ($fileDate->lt($cutoffDate)) {
                    @unlink($fullPath . '/' . $file);
                }
            }
        }
    }

    protected function getLastExportedId(): ?int
    {
        $fullPath = storage_path('app/' . $this->logDirectory);
        $path = $fullPath . '/.last-export-id';

        if (!file_exists($path)) {
            return null;
        }

        $id = file_get_contents($path);
        return $id ? (int) trim($id) : null;
    }

    protected function updateLastExportedId(int $id): void
    {
        $fullPath = storage_path('app/' . $this->logDirectory);
        $path = $fullPath . '/.last-export-id';
        file_put_contents($path, (string) $id);
    }
}

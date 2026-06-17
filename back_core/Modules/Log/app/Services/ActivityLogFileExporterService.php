<?php

namespace Modules\Log\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogFileExporterService
{
    protected string $logDirectory = 'logs/5g-activity-logs';
    protected int $retentionDays = 30;

    public function export(): int
    {
        Storage::disk('local')->makeDirectory($this->logDirectory, 0755, true);

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
        $this->appendToCurrentDayLog($formattedLogs);

        // ذخیره آخرین ID لاگ خروجی گرفته شده
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

    protected function appendToCurrentDayLog(string $content): void
    {
        $filename = $this->getCurrentDayFilename();
        Storage::disk('local')->append("{$this->logDirectory}/{$filename}", $content);
    }

    protected function getCurrentDayFilename(): string
    {
        return 'activity-' . now()->format('Y-m-d') . '.log';
    }

    protected function rotateAndCleanOldLogs(): void
    {
        $files = Storage::disk('local')->files($this->logDirectory);
        $cutoffDate = now()->subDays($this->retentionDays);

        foreach ($files as $file) {
            if (!str_ends_with($file, '.log')) continue;

            if (preg_match('/activity-(\d{4}-\d{2}-\d{2})\.log/', basename($file), $matches)) {
                $fileDate = Carbon::createFromFormat('Y-m-d', $matches[1]);
                if ($fileDate->lt($cutoffDate)) {
                    Storage::disk('local')->delete($file);
                }
            }
        }
    }

    /**
     * خواندن آخرین ID لاگ خروجی گرفته شده
     */
    protected function getLastExportedId(): ?int
    {
        $path = "{$this->logDirectory}/.last-export-id";

        if (!Storage::disk('local')->exists($path)) {
            return null;
        }

        $id = Storage::disk('local')->get($path);
        return $id ? (int) $id : null;
    }

    /**
     * ذخیره آخرین ID لاگ خروجی گرفته شده
     */
    protected function updateLastExportedId(int $id): void
    {
        $path = "{$this->logDirectory}/.last-export-id";
        Storage::disk('local')->put($path, (string) $id);
    }
}

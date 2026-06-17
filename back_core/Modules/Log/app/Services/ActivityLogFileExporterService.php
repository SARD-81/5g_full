<?php

namespace Modules\Log\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogFileExporterService
{
    protected string $logDirectory = 'logs/5g-activity-logs';
    protected int $retentionDays = 30;

    /**
     * اجرای اصلی خروجی‌گیری لاگ‌ها به فایل
     */
    public function export(): void
    {
        $lastExportedAt = $this->getLastExportedTimestamp();

        // دریافت لاگ‌های جدید
        $query = Activity::query()
            ->orderBy('created_at', 'asc');

        if ($lastExportedAt) {
            $query->where('created_at', '>', $lastExportedAt);
        }

        $newLogs = $query->get();

        if ($newLogs->isEmpty()) {
            return; // لاگ جدیدی وجود ندارد
        }

        $formattedLogs = $this->formatLogs($newLogs);

        // نوشتن در فایل روز جاری
        $this->appendToCurrentDayLog($formattedLogs);

        // به‌روزرسانی زمان آخرین خروجی
        $this->updateLastExportedTimestamp($newLogs->last()->created_at);

        // چرخش و پاکسازی لاگ‌های قدیمی
        $this->rotateAndCleanOldLogs();
    }

    /**
     * فرمت کردن لاگ‌ها به فرمت درخواستی
     */
    protected function formatLogs($logs): string
    {
        $output = '';

        foreach ($logs as $log) {
            $timestamp = $log->created_at->format('Y-m-d H:i:s');
            $level = strtoupper($log->log_name ?? 'INFO');
            $event = $log->event ?? 'unknown';
            $description = $log->description ?? '';

            // استخراج اطلاعات مهم از properties
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

        if (isset($properties['user_id'])) {
            $parts[] = 'user_id=' . $properties['user_id'];
        }
        if (isset($properties['ip'])) {
            $parts[] = 'ip=' . $properties['ip'];
        }
        if (isset($properties['server_id'])) {
            $parts[] = 'server_id=' . $properties['server_id'];
        }
        if (isset($properties['module_id'])) {
            $parts[] = 'module_id=' . $properties['module_id'];
        }

        return implode(' | ', $parts);
    }

    /**
     * اضافه کردن لاگ به فایل روز جاری
     */
    protected function appendToCurrentDayLog(string $content): void
    {
        $filename = $this->getCurrentDayFilename();
        Storage::disk('local')->append("{$this->logDirectory}/{$filename}", $content);
    }

    protected function getCurrentDayFilename(): string
    {
        return 'activity-' . now()->format('Y-m-d') . '.log';
    }

    /**
     * چرخش روزانه + پاکسازی لاگ‌های قدیمی
     */
    protected function rotateAndCleanOldLogs(): void
    {
        $files = Storage::disk('local')->files($this->logDirectory);

        $cutoffDate = now()->subDays($this->retentionDays);

        foreach ($files as $file) {
            if (!str_ends_with($file, '.log')) continue;

            // استخراج تاریخ از نام فایل
            if (preg_match('/activity-(\d{4}-\d{2}-\d{2})\.log/', basename($file), $matches)) {
                $fileDate = Carbon::createFromFormat('Y-m-d', $matches[1]);

                if ($fileDate->lt($cutoffDate)) {
                    Storage::disk('local')->delete($file);
                }
            }
        }
    }

    /**
     * خواندن آخرین زمان خروجی از فایل متادیتا
     */
    protected function getLastExportedTimestamp(): ?Carbon
    {
        $path = "{$this->logDirectory}/.last-export-timestamp";

        if (!Storage::disk('local')->exists($path)) {
            return null;
        }

        $timestamp = Storage::disk('local')->get($path);
        return $timestamp ? Carbon::parse($timestamp) : null;
    }

    /**
     * ذخیره آخرین زمان خروجی
     */
    protected function updateLastExportedTimestamp(Carbon $timestamp): void
    {
        $path = "{$this->logDirectory}/.last-export-timestamp";
        Storage::disk('local')->put($path, $timestamp->toDateTimeString());
    }
}

<?php

namespace Modules\Backup\Services;

use Carbon\Carbon;

class RunBackupDueLastRun
{
    public function handel ($lastRun, string $schedule, int $toleranceMinutes = 0, string $tz = 'Asia/Tehran'): bool
    {
        if (!preg_match('/^\s*(\d{1,2})\s+(\d{1,2}):(\d{2})\s*$/', $schedule, $m))
            return false;

        $intervalDays = (int)$m[1];
        $hour         = (int)$m[2];
        $minute       = (int)$m[3];

        $now = Carbon::now($tz);
        $last = $lastRun ? Carbon::parse($lastRun, $tz) : null;

        if (! $last) return false;

        $daysPassed = $last->copy()->startOfDay()->diffInDays($now->copy()->startOfDay());

        if ($daysPassed < $intervalDays) return false;

        $scheduledToday = $now->copy()->setTime($hour, $minute, 0);

        if ($toleranceMinutes > 0) return $now->betweenIncluded($scheduledToday, $scheduledToday->copy()->addMinutes($toleranceMinutes));

        return $now->format('H:i') === $scheduledToday->format('H:i');
    }
}

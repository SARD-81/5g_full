<?php

use Illuminate\Support\Facades\Log;
use Modules\Server\Models\ModuleSchedule;
use Illuminate\Support\Facades\Schedule;
use Modules\Backup\Models\BackupConfig;
use Modules\Backup\Services\BackupService;
use Modules\Server\Service\Schedule\ModuleScheduleService;

Schedule::call(function () {
//    Log::info("Schedule started.");

    $backupService = new BackupService();
    $runBackup = new \Modules\Backup\Services\RunBackupDueLastRun();
    $moduleScheduleService = new ModuleScheduleService();
    $backupConfigs = BackupConfig::get();

    foreach ($backupConfigs as $backupConfig) { // run backup as all servers

        if ($runBackup->handel($backupConfig->last_run_backup_at ?? $backupConfig->created_at, $backupConfig->run_backup_at))
            $backupService->handle($backupConfig);
    }


    $moduleSchedules = ModuleSchedule::query() // run schedule module
        ->whereIn('status', [ModuleSchedule::FAILED, ModuleSchedule::WAITING])
        ->where('run_scheduled_at', '<=', now())
        ->get();

    foreach ($moduleSchedules as $moduleSchedule) {
//        Log::info("running schedule with id: " . $moduleSchedule->id);
        $moduleScheduleService->handle($moduleSchedule);
    }

//    if ($moduleSchedule) $moduleScheduleService->handle($moduleSchedule);

})
    ->everyMinute()
    ->name('module-schedule');
//    ->withoutOverlapping();


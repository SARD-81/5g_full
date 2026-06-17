<?php

namespace Modules\Backup\Services;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\File;
use Modules\Backup\Models\BackupConfig;
use Modules\Backup\Models\BackupHistory;
use Modules\Server\Models\Server;

class BackupService
{
    public function handle(BackupConfig $backupConfig) : void
    {
        $backupConfig->load('history');

        // ایجاد رکورد backup history
        $backupHistory = BackupHistory::create([
            'start_time'       => now(),
            'status'           => BackupHistory::RUNING,
            'backup_config_id' => $backupConfig['id']
        ]);

        try {

            $storagePath = $backupConfig['destination_path'] . now()->toString();

            if (file_exists($storagePath)) File::deleteDirectory($storagePath);

            File::makeDirectory($storagePath, 0777, true, true); // ایجاد پوشه backup

            $servers = Server::with('modules')->get();

            // ایجاد پوشه برای هر سرور
            foreach ($servers as $server) {
                $serverFolder = "{$storagePath}/{$server->name}";
                File::makeDirectory($serverFolder);

                // ایجاد فایل YAML هر ماژول با استفاده از YamlParserService اصلاح‌شده
                foreach ($server->modules as $module) {
                    $configFile = "{$serverFolder}/{$module->service_key}.yaml";

                    // بررسی داده واقعی ماژول (فقط برای تست، بعداً می‌توان حذف کرد)
                    // echo "Current config JSON for module {$module->service_key}:\n";
                    // var_dump($module->pivot->current_config);

                    // استفاده از YamlParserService اصلاح‌شده
                    $yamlContent = \Modules\Server\Service\Parser\YamlParserService::convertJsonToYaml(
                        $module->pivot->current_config
                    );

                    File::put($configFile, $yamlContent);
                }
            }

            activity('backup_modules')
                ->event('schedule')
                ->withProperties([
                    'type-log' => 'schedule',
                    'time'     => now(),
                    'destination_path' => $storagePath,
                    'servers'  => $servers->pluck('name')->toArray(),
                ])
                ->log('run backup module schedule successful');

            $backupHistory->update([
                'name'        => now()->toString(),
                'destination_path' => $backupConfig->destination_path,
                'message'     => 'backup successful',
                'status'      => BackupHistory::SUCCESSFULY,
                'servers'     => $servers->pluck('name'),
                'finish_time' => now()
            ]);

        } catch (\Exception $e) {

            activity('backup_modules')
                ->event('schedule')
                ->withProperties([
                    'type-log' => 'schedule',
                    'time'     => now(),
                    'destination_path' => $storagePath,
                    'servers'  => $servers->pluck('name')->toArray(),
                    'errors'   => $e->getMessage()
                ])
                ->log('Problem in process run backup module schedule');

            $backupHistory->update([
                'name'        => now()->toString(),
                'destination_path' => $backupConfig->destination_path,
                'message'     => $e->getMessage(),
                'servers'     => $servers->pluck('name'),
                'status'      => BackupHistory::FAILED,
                'finish_time' => now()
            ]);
        }
    }
}

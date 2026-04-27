<?php

namespace Modules\Server\Service\Schedule;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Models\ModuleSchedule;
use Modules\Server\Models\Server;
use Modules\Server\Utility\CommandOutputAnalyzerService;
use Symfony\Component\Yaml\Yaml;

class ModuleScheduleService
{
    public function handle(ModuleSchedule $moduleSchedule)
    {
        // شروع اجرای Schedule
        $moduleSchedule->update(['status' => ModuleSchedule::RUNNING]);

        if ($moduleSchedule['status'] == ModuleSchedule::SUCCESS) return; // اگر قبلاً موفق بوده، خروج

        try {
            $pivotData = $moduleSchedule->module->servers()
                ->where('server_id', $moduleSchedule['server_id'])
                ->first();

            // اگر سرور هنوز pivot ندارد، اضافه می‌کنیم
            if (!$pivotData) {
                $moduleSchedule->module->servers()->syncWithoutDetaching([
                    $moduleSchedule->server_id => [
                        'current_config' => $moduleSchedule->config,
                        'initial_config' => $moduleSchedule->config
                    ]
                ]);
            }

            // =========================
            // امن سازی parse config
            // =========================
            $configContent = $moduleSchedule->config;
            $parsedConfig = $this->parseConfigSafely($configContent, $moduleSchedule->config_file ?? null);
            $jsonConfig = json_encode($parsedConfig, JSON_PRETTY_PRINT);

            // بازخوانی pivotData بعد از sync
            $pivotData = $moduleSchedule->module
                ->servers()
                ->where('server_id', $moduleSchedule->server_id)
                ->first();

            // به روزرسانی DB
            DB::transaction(function () use ($moduleSchedule, $jsonConfig, $pivotData) {
                DB::table('module_server')
                    ->where('module_id', $moduleSchedule->module_id)
                    ->where('server_id', $moduleSchedule->server_id)
                    ->update([
                        'previous_config' => $pivotData->pivot->current_config,
                        'current_config' => $jsonConfig,
                    ]);
            });

            // ارسال به سرور
            $outputCommand = $this->sendConfigToServer(
                $moduleSchedule['username_ssh'],
                $moduleSchedule['password_ssh'],
                $moduleSchedule->module->service_key,
                $moduleSchedule['config'],
                $moduleSchedule->server,
                'moduleSchedule',
                'scheduleService',
                $moduleSchedule->port_ssh,
            );

            // تحلیل خروجی
            $commandWarning = !empty($outputCommand) ? CommandOutputAnalyzerService::extractErrors($outputCommand) : null;
            if ($commandWarning) throw ValidationException::withMessages($commandWarning);

            $moduleSchedule->update(['status' => ModuleSchedule::SUCCESS]);

            activity('module schedule')
                ->event('schedule')
                ->withProperties([
                    'type-log' => 'schedule',
                    'time' => now(),
                    'module_schedule' => $moduleSchedule,
                ])
                ->log('run module schedule schedule successfully');

        } catch (\Exception $e) {
            $moduleSchedule->update(['status' => ModuleSchedule::FAILED]);
            activity('module schedule')
                ->event('schedule')
                ->withProperties([
                    'type-log' => 'schedule',
                    'time' => now(),
                    'module_schedule' => $moduleSchedule,
                    'errors' => $e->getMessage()
                ])
                ->log('Problem in process run module schedule schedule came into being');

            throw $e;
        }
    }

    /**
     * تحلیل امن config
     */
    private function parseConfigSafely(string $configContent, ?string $fileName)
    {
        $extension = $fileName ? strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) : null;
        $trimmed = ltrim($configContent);

        try {
            if ($extension && in_array($extension, ['yaml', 'yml', 'yaml.in'])) {
                return Yaml::parse($configContent);
            } elseif ($extension === 'json' || str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                return json_decode($configContent, true, 512, JSON_THROW_ON_ERROR);
            } else {
                // سایر فرمت‌ها (conf، conf.in) را بدون تغییر نگه می‌داریم
                return $configContent;
            }
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'config_file' => 'Configuration parsing failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function sendConfigToServer(
        string $username,
        string $password,
        string $moduleName,
        string $yamlContent,
        Server $server,
        string $typeCommand,
        string $method,
        int    $port = 22,
    )
    {
        if ($server['is_down'] == Server::OFF) throw ValidationException::withMessages(['server.down' => 'this server: ' . $server['name'] . ' is off']);

        if (!$server['path_config']) throw ValidationException::withMessages(['server.path_config' => 'You did not specify a configuration address config']);

        if (!$server['path_run_config']) throw ValidationException::withMessages(['server.path_run_config' => 'You did not specify a configuration address run config']);

        $sshHelper = new sshHelper($server->ip, $username, $password, $port);

        $commandUpdateFileModule = 'echo ' . escapeshellarg($yamlContent) . ' > ' . $server['path_config'] . $moduleName . '.yaml';
        return $sshHelper->runCommandModule($commandUpdateFileModule, $typeCommand, $method);
    }
}

<?php

namespace Modules\SystemSetting\Service;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Modules\SystemSetting\Models\SystemSettings;

class KpiService
{
    private $system_setting;

    private $kpi_file_name;
    private $kpi_address;
    public function __construct()
    {
        $this->system_setting = SystemSettings::first();

        $this->kpi_file_name = $this->system_setting?->kpi_file_name;
        $this->kpi_address = $this->system_setting?->kpi_address;
    }
//    protected string $kpi_file = 'kpi.txt';

    public function initFromFile($uploadedFile): void
    {
        if ($this->kpi_file_name && Storage::disk('local')->exists($this->kpi_file_name)) {
            Storage::disk('local')->delete($this->kpi_file_name);
//            abort(422, 'kpi file is already initiated!');
        }
        $name = $uploadedFile->getClientOriginalName();
        $content = file_get_contents($uploadedFile->getRealPath());
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        $processedLines = [];

        foreach ($lines as $line) {
            // استخراج id و title از ساختار {id, "title"}
            if (preg_match('/\{\s*(\d+)\s*,\s*["\'](.*?)["\']\s*}/', $line, $matches)) {
                $id = $matches[1];
                $title = $matches[2];
                $processedLines[] = sprintf("{%d, \"%s\", \"off\"}", $id, $title);
            }
        }

        Storage::disk('local')->put($name, implode(PHP_EOL, $processedLines));

        $this->system_setting->update([
            'kpi_file_name' => $name,
        ]);

        $this->kpi_file_name = $name;
    }

    public function setPath($path)
    {
        $set_new_statuses = false;

        if ($this->kpi_address && $this->kpi_file_name && Storage::disk('local')->exists($this->kpi_file_name)) {
            $set_new_statuses = true;
        }
        $path = trim($path, "\"'");

        $this->system_setting->update([
            'kpi_address' => $path
        ]);

        if ($set_new_statuses) {
            $this->setOnNewKpi($path);
        }
        $this->kpi_address = $path;

    }

    private function setOnNewKpi($path)
    {
        $items = $this->getAll();

        foreach ($items as $item) {
            $this->turnOn($item, $path);
        }
    }

    public function getAll(): array
    {
        if (!$this->kpi_file_name || !Storage::disk('local')->exists($this->kpi_file_name)) {
            return [];
        }

        $content = Storage::disk('local')->get($this->kpi_file_name);
        $lines = preg_split('/\r\n|\r|\n/', $content, -1, PREG_SPLIT_NO_EMPTY);

        $items = [];

        foreach ($lines as $line) {
            // الگو: {id, "title", "status"}
            if (preg_match('/\{(\d+),\s*"(.*?)",\s*"(on|off)"}/', $line, $matches)) {
                $items[] = [
                    'id' => (int)$matches[1],
                    'title' => $matches[2],
                    'status' => $matches[3],
                ];
            }
        }

        return $items;
    }

    public function all(): array
    {
        $content = Storage::disk('local')->get($this->kpi_file_name);
        $lines = explode(PHP_EOL, trim($content));

        $items = [];
        foreach ($lines as $line) {
            if (preg_match('/\{(\d+),\s*"(.*?)",\s*"(on|off)"}/', $line, $matches)) {
                $items[] = [
                    'id' => (int)$matches[1],
                    'title' => $matches[2],
                    'status' => $matches[3],
                ];
            }
        }
        return $items;
    }

    public function updateStatus(int $id, string $status)
    {
        $items = $this->getAll();
        foreach ($items as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = $status;
            }
        }

        $lines = array_map(fn($item) => sprintf("{%d, \"%s\", \"%s\"}", $item['id'], $item['title'], $item['status']), $items);
        Storage::disk('local')->put($this->kpi_file_name, implode(PHP_EOL, $lines));
    }


    public function turnOn(array $item, $filePath = null)
    {
        $filePath = $filePath ?? $this->kpi_address;
        $filePath = $filePath . $this->kpi_file_name;
//        dd($filePath);
        try {
            // ساخت خط جدید
            $kpiLine = sprintf(
                '{%d, "%s"},',
                $item['id'],
                $item['title']
            );

            // اگر مسیر فایل وجود ندارد، فایل را بساز (و پوشه را در صورت نیاز ایجاد کن)
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if (!file_exists($filePath)) {
                file_put_contents($filePath, '');
            }

            // خواندن محتوا
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // اگر خط وجود دارد، دوباره اضافه نکن
            if (in_array($kpiLine, $lines, true)) {
                return [
                    'success' => true,
                    'output' => 'Line already exists',
                    'error' => null,
                ];
            }

            // افزودن خط جدید
            file_put_contents($filePath, $kpiLine . PHP_EOL, FILE_APPEND);

            return [
                'success' => true,
                'output' => 'Line added successfully',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'output' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function turnOff(array $item, $filePath = null)
    {
        $filePath = $filePath ?? $this->kpi_address . $this->kpi_file_name;

        try {
            if (!file_exists($filePath)) {
                return [
                    'success' => false,
                    'output' => 'File not found',
                    'error' => null,
                ];
            }

            $targetLine = sprintf(
                '{%d, "%s"},',
                $item['id'],
                $item['title']
            );

            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $originalCount = count($lines);
            $newLines = [];
            $found = false;

            foreach ($lines as $line) {
                if (trim($line) === $targetLine) {
                    $found = true;
                    continue;
                }
                $newLines[] = $line;
            }

            if ($found) {

                file_put_contents($filePath, implode(PHP_EOL, $newLines) . PHP_EOL);

                return [
                    'success' => true,
                    'output' => 'Item removed successfully',
                    'error' => null,
                ];
            }

            return [
                'success' => true, // عملیات با موفقیت تمام شده حتی اگر فایلی پیدا نشده (Idempotent)
                'output' => 'Item not found in file',
                'error' => null,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'output' => null,
                'error' => $e->getMessage(),
            ];
        }
    }


    protected string $filePath = '/var/www/test/Kpi_Str_Map.txt';

    public function runSecureCommands($filePath, $password)
    {
        // ۱. بررسی صحت پسورد (sudo -v اعتبار پسورد را چک می‌کند)
        if (!$this->isPasswordCorrect($password)) {
            return [
                'status' => false,
                'message' => 'پسورد سیستم‌عامل اشتباه است یا کاربر اجازه sudo ندارد.',
                'exit_code' => null
            ];
        }

        $commands = [
            "sudo -S chown apache:apache {$filePath}",
            "sudo -S chmod 664 {$filePath}",
            "sudo -S chcon -t httpd_sys_rw_content_t {$filePath}"
        ];

        foreach ($commands as $command) {
            // اجرا با ارسال پسورد به ورودی استاندارد (Standard Input)
            $result = Process::input($password . "\n")->run($command);

            if ($result->failed()) {
                \Log::error("خطا در اجرای دستور: {$command}", [
                    'exit_code' => $result->exitCode(),
                    'error_output' => $result->errorOutput()
                ]);

                return [
                    'status' => false,
                    'message' => "خطا در اجرای دستور: $command",
                    'exit_code' => $result->exitCode(),
                    'error' => $result->errorOutput()
                ];
            }
        }

        return [
            'status' => true,
            'message' => 'تمامی دستورات با موفقیت و Exit Code 0 اجرا شدند.',
        ];
    }

    /**
     * بررسی صحت پسورد با استفاده از دستور sudo -v
     */



    public function turnOnSSH($item, $remote_path, $server_ip, $username, $password)
    {
        if ($server_ip && $remote_path) {
            $kpiLine = sprintf(
                '{%d, "%s"},',
                $item['id'],
                $item['title']
            );

            $command = sprintf(
                'sshpass -p %s ssh -o StrictHostKeyChecking=no %s@%s "echo \'%s\' >> %s"',
                escapeshellarg($password),
                escapeshellarg($username),
                escapeshellarg($server_ip),
                $kpiLine,
                escapeshellarg($remote_path)
            );

//            dd($command);

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new HttpResponseException(response()->json([
                    'error' => 'SSH command failed',
                    'output' => $process->getErrorOutput(),
                    'success' => false,
                ], 500));
            }

            $output = $process->getIncrementalOutput();
            $error = $process->getIncrementalErrorOutput();
            $success = $process->isSuccessful();

        }

        return [
            'output' => $output ?? null,
            'error' => $error ?? null,
            'success' => $success ?? true,
        ];
    }
    public function turnOffSSH($item, $remote_path, $server_ip, $username, $password)
    {
        if ($server_ip && $remote_path) {
            $kpiLine = sprintf(
                '{%d, %s},',
                $item['id'],
                $item['title']
            );

            $escapedPattern = addcslashes($kpiLine, "'\\/\"$");

            $command = sprintf(
                'sshpass -p %s ssh -o StrictHostKeyChecking=no %s@%s "sed -i \'/%s/d\' %s"',
                escapeshellarg($password),
                escapeshellarg($username),
                escapeshellarg($server_ip),
                $escapedPattern,
                escapeshellarg($remote_path)
            );

//            dd($command);

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                return response()->json([
                    'error' => 'SSH delete command failed',
                    'output' => $process->getErrorOutput(),
                    'success' => false,
                ], 500);
            }
        }

        return [
            'output' => $output ?? null,
            'error' => $error ?? null,
            'success' => $success ?? true,
        ];
    }
}

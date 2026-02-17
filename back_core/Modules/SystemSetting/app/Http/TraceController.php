<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Modules\SystemSetting\Http\Requests\Trace\TraceServerRequest;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

class TraceController extends Controller
{

    private $host_ip = '192.168.0.79';


    public function __construct()
    {
    }
    private function processStarter($process, $server)
    {
        $process->setTimeout(20);

        $output = '';

        $process->start(function ($type, $buffer) use ($server, &$output, &$hasError) {
            $output .= $buffer;

            if (Process::OUT === $type) {
                echo "$server->ip OUT: $buffer";
            } else {
                echo "$server->ip ERR: $buffer";
            }
        });

        return [
            'process' => $process,
            'output' => $output,
        ];
    }


//          start trace
    private function processRuner($process, $server)
    {
        $process->setTimeout(300);
        $process->setIdleTimeout(60);

        Log::info("Starting process for server {$server->ip}: " . $process->getCommandLine());

        $output = '';
        $error = '';

        try {
            $process->run(function ($type, $buffer) use ($server, &$output, &$error) {
                if (Process::OUT === $type) {
                    $output .= $buffer;
                    Log::info("Server {$server->ip} OUT: " . trim($buffer));
                } else {
                    $error .= $buffer;
                    Log::error("Server {$server->ip} ERR: " . trim($buffer));
                }
            });

            if ($process->hasBeenSignaled()) Log::warning("Process terminated by signal: " . $process->getTermSignal());


            if ($process->hasBeenSignaled()) {
                $signal = $process->getTermSignal();
                Log::warning("Process for server {$server->ip} terminated by signal: " . $signal);

                if ($signal === 15) {
                    return [
                        'process' => $process,
                        'output' => $output,
                        'error' => $error . " [Process was gracefully terminated with SIGTERM]",
                        'success' => true,
                        'exit_code' => 0,
                        'command' => $process->getCommandLine(),
                        'signaled' => true
                    ];
                }
            }

            $output .= $process->getIncrementalOutput();
            $error .= $process->getIncrementalErrorOutput();
            $success = $process->isSuccessful();

            return [
                'process' => $process,
                'output' => $output,
                'error' => $error,
                'success' => $success,
                'exit_code' => $process->getExitCode(),
                'command' => $process->getCommandLine()
            ];

        } catch (ProcessSignaledException $e) {
            Log::error("ProcessSignaledException for server {$server->ip}: " . $e->getMessage());
            return [
                'process' => $process,
                'output' => $output,
                'error' => $error . " [Process signaled: " . $e->getMessage() . "]",
                'success' => false,
                'exit_code' => -1,
                'command' => $process->getCommandLine(),
                'signaled' => true
            ];
        } catch (\Exception $e) {
            Log::error("Exception in processRuner for server {$server->ip}: " . $e->getMessage());
            return [
                'process' => $process,
                'output' => $output,
                'error' => $error . " [Exception: " . $e->getMessage() . "]",
                'success' => false,
                'exit_code' => -1,
                'command' => $process->getCommandLine()
            ];
        }
    }


    private function commandHelperStartServer(
        string $username,
        string $password,
        Server $server,
        array  $interface,
        array  $moduleName = null
    )
    {
        $ip = $server['ip'];

        $impledModuleName = !empty($moduleName) ? implode(' ', $moduleName) : null;
        $impledInterfaceName = !empty($interface) ? '-i ' . implode(' -i ', $interface) : 'any';

        $tsharkControlPath = base_path('Modules/SystemSetting/app/Http/Services/Bash/tshark-control.sh');
        $setShPath = base_path('Modules/SystemSetting/app/Http/Services/Bash/set.sh');
        $remotePath = env('TRACE_REMOTE_PATH');

//        $sshOptions = "-T -o StrictHostKeyChecking=no";
        $sshOptions = "-T -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null";

        $makeDirCommand = "sshpass -p '{$password}' ssh {$sshOptions} {$username}@{$ip} 'mkdir -p {$remotePath}'";

        $commandScpTsharkControl = "sshpass -p '{$password}' scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null {$tsharkControlPath} {$username}@{$ip}:{$remotePath}";

        $commandScpSetSh = "sshpass -p '{$password}' scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null {$setShPath} {$username}@{$ip}:{$remotePath}";

        $permissionCommand =
            "sshpass -p '{$password}' ssh {$sshOptions} {$username}@{$ip} "
            . "'echo \"{$password}\" | sudo -S -p \"\" chmod 777 -R {$remotePath}'";

//        run tshark service
        $commandRunScript =
            "sshpass -p '{$password}' ssh {$sshOptions} {$username}@{$ip} "
            . "'echo \"{$password}\" | sudo -S bash {$remotePath}tshark-control.sh start -i {$impledInterfaceName} -m {$impledModuleName} > {$remotePath}tshark.log 2>&1 &'";

        return [
            'makeDirCommand' => $makeDirCommand,
            'commandScpTsharkControl' => $commandScpTsharkControl,
            'commandScpSetSh' => $commandScpSetSh,
            'permissionCommand' => $permissionCommand,
            'commandRunScript' => $commandRunScript,
        ];
    }

    public function traceServerStart(TraceServerRequest $request)
    {
        $credentials = $request->validated();

        try {

            $results = [];
            $processes = [];

            foreach ($request['servers'] as $server) {

                $serverIndex = array_search($server['id'], array_column($credentials['servers'], 'id'));
                $username = $credentials['servers'][$serverIndex]['username'];
                $password = $credentials['servers'][$serverIndex]['password'];
                $interface = $credentials['servers'][$serverIndex]['interface'] ?? [];
                $port = $credentials['servers'][$serverIndex]['port'] ?? 22;

                $moduleName = isset($credentials['servers'][$serverIndex]['module_ids'])
                    ? Module::whereIn('id', $credentials['servers'][$serverIndex]['module_ids'])->pluck('name')->toArray()
                    : null;

                $commands = $this->commandHelperStartServer($username, $password, $server, $interface, $moduleName);

                $sshHelper = new sshHelper($server['ip'], $username, $password, $port, 7);
                $sshHelper->testConnection();


                foreach (['makeDirCommand', 'commandScpTsharkControl', 'commandScpSetSh',
                             'permissionCommand', 'commandRunScript'] as $key) {
                    $proc = Process::fromShellCommandline($commands[$key]);
                    $started = $this->processRuner($proc, $server);
                    $processes[$server['ip']][] = [
                        'name' => $key,
                        'process' => $started['process'],
                        'output' => $started['output'],
                        'error' => $started['error'],
                        'success' => $started['success'],
                        'exit_code' => $started['exit_code']
                    ];
                }
            }

            foreach ($processes as $ip => $list) {
                foreach ($list as $item) {
                    $item['process']->wait();
                    $results[$ip][$item['name']] = [
                        'status' => $item['success'],
                        'output' => $item['output'],
                        'error' => $item['error'],
                        'exit_code' => $item['exit_code']
                    ];
                }
            }


            return response()->json([
                'status' => true,
                'message' => 'Trace started successfully.',
                'results' => $results,
            ], 200);

        } catch (\Exception $e) {
//                return response()->json(['status' => false, 'message'=> $e->getMessage(),], 422);
            throw $e;
        }
    }


//        stop trace
    private function commandHelperStopServer(
        string $username,
        string $password,
        Server $server,
        array  $interface,
        mixed  $moduleIdentifier,
        string $currentServerPassword,
        array  $moduleName = null
    )
    {
        $moduleName = $server->modules()->pluck('name')->toArray();
        $impledModuleName = !empty($moduleName) ? implode(' ', $moduleName) : null;
        $impledInterfaceName = !empty($interface) ? implode(' -i ', $interface) : 'any';

        // --- مسیرهای اصلاح شده ---
        // مسیر اسکریپت ها روی سرور ریموت (از .env)
        $remoteScriptPath = env('TRACE_REMOTE_PATH');
        // مسیر فایل pcap روی سرور ریموت (بر اساس اسکریپت tshark-control.sh)
        $remotePcapPath = '/tmp/' . $server['ip'] . '_' . $impledInterfaceName . '.pcapng';

        // مسیر ذخیره سازی لوکال (روی سرور لاراول) - برای دسترسی عمومی و داکر
        // مطمئن شوید که php artisan storage:link اجرا شده است
        $localCapturePath = storage_path('app/public/captures');

        // مسیر لاگ لوکال (روی سرور لاراول)
        $localLogStoragePath = env('TRACE_LOG_FILE_PATH'); // فرض می کنیم این مسیر صحیح است، مثلا storage_path('app/logs')

        // نام فایل نهایی pcap
        $mergedFileName = "{$server['ip']}_{$impledInterfaceName}_final.pcapng";
        // مسیر کامل فایل pcap نهایی روی سرور لاراول
        $localPcapFinalPath = $localCapturePath . '/' . $mergedFileName;

        // --- اصلاح منطق لاگ ---
        // نام فایل های لاگ روی سرور ریموت
        $remoteLogFileNames = array_map(function ($name) {
            return "bbdh-{$name}.log";
        }, $moduleName);
        // مسیر کامل فایل های لاگ روی سرور ریموت
        $remoteLogPaths = implode(' ', array_map(function ($file) use ($remoteScriptPath) {
            return "{$remoteScriptPath}{$file}";
        }, $remoteLogFileNames));

        $localLogFileNamesImplode = implode(' ', $remoteLogFileNames);


        $commandStopTshark = "sshpass -p '{$password}' ssh -tt -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null {$username}@{$server['ip']} "
            . "\"mkdir -p /tmp/tshark && echo '{$password}' | sudo -S bash {$remoteScriptPath}tshark-control.sh stop -i {$impledInterfaceName} -m {$impledModuleName}\"";

        // 2. کپی فایل Pcap (مسیر لوکال اصلاح شد)
        $commandScpPcapFile = "mkdir -p {$localCapturePath} && sshpass -p '{$password}' scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "
            . "{$username}@{$server['ip']}:'{$remotePcapPath}' {$localPcapFinalPath}";

        // 3. کپی لاگ ها (مسیر ریموت و لوکال اصلاح شد)
        $scpLogCommand = "mkdir -p {$localLogStoragePath} && sshpass -p '{$password}' scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "
            . "{$username}@{$server['ip']}:\"{$remoteLogPaths}\" {$localLogStoragePath}/";

        // 4. ادغام لاگ ها (مسیر cd اصلاح شد)
        $mergeMmeLogCommand = "cd {$localLogStoragePath} && cat {$localLogFileNamesImplode} > {$localLogStoragePath}mme.log";

        // 5. اجرای اسکریپت set.sh (بدون تغییر)
        $setShRun = "echo '{$currentServerPassword}' | su - root -c 'mkdir -p {$localCapturePath} && {$localCapturePath}set.sh {$moduleIdentifier}'";


//        $localCapturePath = storage_path('app/public/captures');
//        $logFile = $localCapturePath . '/webshark_run_' . $server->ip . '.log';


//        sudo usermod -aG docker bbdh
//        sudo systemctl restart httpd
//        sudo -u bbdh docker ps

        $dockerRunWireShark = "docker run -d --rm -p 8085:8085 -v {$localCapturePath}:/captures:ro,Z ghcr.io/qxip/webshark:latest";

        // 6. اجرای WebShark (مسیر volume اصلاح شد تا به pcap ها اشاره کند)
//        $dockerRunWireShark = "echo '{$currentServerPassword}' | sudo -S bash -c "
//            . escapeshellarg("mkdir -p /tmp/tshark && mkdir -p {$localCapturePath} && "
//                . "docker run -d --rm --name webshark_{$server->ip} -p 8085:8085 -v {$localCapturePath}:/captures:ro,Z "
//                . "ghcr.io/qxip/webshark:latest > {$logFile} 2>&1");


        return [
            'commandStopTshark' => $commandStopTshark,
            'commandScpPcapFile' => $commandScpPcapFile,
            'scpMmeLogCommand' => $scpLogCommand,
            'mergeMmeLogCommand' => $mergeMmeLogCommand,
            // commandMergePcap حذف شد
            'setShRun' => $setShRun,
            'dockerRunWireShark' => $dockerRunWireShark,
            'mergedFileName' => $mergedFileName, // نام فایل را برای ساخت لینک برمی گردانیم
            'localCapturePath' => $localCapturePath // مسیر را برای ساخت لینک برمی گردانیم
        ];
    }

    public function traceServerStop(TraceServerRequest $request)
    {
        $credentials = $request->validated();

        $results = [];
        $processes = [];
        $websharkFiles = [];
        $downloadLinks = [];
        $localCapturePaths = [];

        foreach ($request['servers'] as $server) {

            $serverIndex = array_search($server['id'], array_column($credentials['servers'], 'id'));
            $username = $credentials['servers'][$serverIndex]['username'];
            $password = $credentials['servers'][$serverIndex]['password'];
            $interface = $credentials['servers'][$serverIndex]['interface'] ?? [];
            $port = $credentials['servers'][$serverIndex]['port'] ?? 22;

            $moduleIdentifier = $credentials['servers'][$serverIndex]['module_identifier'] ?? null;

            $moduleName = isset($credentials['servers'][$serverIndex]['module_ids'])
                ? Module::whereIn('id', $credentials['servers'][$serverIndex]['module_ids'])->pluck('name')->toArray()
                : null;

            $commands = $this->commandHelperStopServer($username, $password, $server, $interface, $moduleIdentifier, $credentials['current_server_password'], $moduleName);

            $sshHelper = new sshHelper($server['ip'], $username, $password, $port, 7);
            $sshHelper->testConnection();

            $localCapturePaths[$server['ip']] = $commands['localCapturePath'];

            // commandMergePcap از لیست حذف شد
            $run_commands = [
                'commandStopTshark',
                'commandScpPcapFile',
                // 'commandMergePcap', // حذف شد
                'scpMmeLogCommand',
                'mergeMmeLogCommand',
                'dockerRunWireShark'
            ];

            // setShRun را فقط در صورت وجود moduleIdentifier اجرا کنید
            if (!empty($moduleIdentifier)) {
                $run_commands[] = 'setShRun';
            }

            foreach ($run_commands as $key) {
                // اگر دستوری در $commands وجود نداشت (مثلا setShRun) از آن بپر
                if (!isset($commands[$key])) continue;

                $proc = Process::fromShellCommandline($commands[$key]);
                $started = $this->processRuner($proc, $server);
                $processes[$server['ip']][] = [
                    'name' => $key,
                    'process' => $started['process'],
                    'output' => $started['output'],
                    'error' => $started['error'],
                    'success' => $started['success'],
                    'exit_code' => $started['exit_code']
                ];
            }

            // ساخت لینک WebShark و دانلود
            // این لینک ها بر اساس اجرای موفقیت آمیز دستورات ساخته می شوند
            $localPcapName = $commands['mergedFileName'];
            $websharkFiles[$server['ip']] = "http://{$this->host_ip}:8085/webshark?path=/captures/{$localPcapName}";

            // این لینک نیازمند اجرای `php artisan storage:link` است
            $downloadLinks[$server['ip']] = url("storage/captures/{$localPcapName}");
        }

        // صبر کردن تا همه‌ی پروسس‌ها تموم بشه
        foreach ($processes as $ip => $list) {
            foreach ($list as $item) {
                $item['process']->wait();
                $results[$ip][$item['name']] = [
                    'status' => $item['success'],
                    'output' => $item['output'],
                    'error' => $item['error'],
                    'exit_code' => $item['exit_code']
                ];
            }
        }

        // بازگرداندن پاسخ کامل به همراه لینک ها
        return response()->json([
            'status' => true,
            'message' => 'Trace stopped and data loaded into WebShark successfully.',
            'results' => $results,
            'webshark' => "http://{$this->host_ip}:8085/webshark", // لینک عمومی وب شرک
            'webshark_links' => $websharkFiles, // لینک های هر فایل
            'download_links' => $downloadLinks, // لینک های دانلود
            'localCapturePaths' => $localCapturePaths,
        ], 200);
    }

}

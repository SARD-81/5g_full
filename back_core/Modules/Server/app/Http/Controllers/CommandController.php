<?php

namespace Modules\Server\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Http\Requests\Module\restartServiceModuleRequest;
use Modules\Server\Http\Requests\SshServer\SshServerRequest;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Modules\Server\Utility\CommandOutputAnalyzerService;
use Modules\Server\Utility\ModuleIdentity;
use Modules\SystemSetting\Http\Requests\ShowInterfaceVmRequest;

class CommandController extends Controller
{
    public function __construct()
    {}

    public function showInterfaceVm(ShowInterfaceVmRequest $request)
    {
        $credentials = $request->validated();
        $serverIds   = array_column($credentials['servers'], 'id');

        try {
            $servers = Server::whereIn('id', $serverIds)->get();

            $command = 'ip link show';

            $result = [];
            foreach ($servers as $server) {

                $serverIndex = array_search(
                    $server->id,
                    array_column($credentials['servers'], 'id')
                );

                if ($serverIndex === false) {
                    throw new \Exception("Server id {$server->id} not found in request payload.");
                }

                $connectionData = [
                    'username' => $credentials['servers'][$serverIndex]['username'],
                    'password' => $credentials['servers'][$serverIndex]['password'],
                    'port'     => $credentials['servers'][$serverIndex]['port'] ?? 22,
                ];

                $result[$server->name] = [
                    'server_id'       => $server->id,
                    'server_name'     => $server->name,
                    'command_output'  => $this->runCommandModuleToServer(
                        $connectionData,
                        $command,
                        $server,
                        'show-interface-vm',
                        'showInterfaceVm'
                    ),
                ];
            }

            return $result;

        } catch (\Exception $e) {
            throw ValidationException::withMessages(['warning' => $e->getMessage()]);
        }
    }

    // service module
    private function runCommandModuleToServer(
        array $credentials,
        string $command,
        Server $server,
        string $typeCommand,
        string $method,
        int $timeout = 5
    ) {
        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        // ✅ IMPORTANT: read port from credentials (coming from frontend)
        $port = isset($credentials['port']) ? (int) $credentials['port'] : 22;

        // optional debug log (remove later if you want)
        // Log::info("SSH CONNECT -> IP={$server->ip} PORT={$port} USER={$username}");

        if ($server->is_down == Server::OFF) {
            throw ValidationException::withMessages(['server' => 'this server off']);
        }

        try {
            $sshHelper = new SshHelper($server->ip, $username, $password, $port, $timeout);

            $outputCommand = $sshHelper->runCommandModule($command, $typeCommand, $method);

            $errors = CommandOutputAnalyzerService::extractErrors($outputCommand);
            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            return $outputCommand;

        } catch (ValidationException $e) {
            throw $e;

        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
            $message = preg_replace('/\x1b\[[0-9;]*m/', '', $message);
            $message = preg_replace('/\r?\n.*?\[root@localhost.*?$/', '', $message);

            preg_match_all('/\b(FATAL|ERROR):\s.*?(?=\s\(.*?\)|$)/m', $message, $matches);

            $formattedMessages = $matches[0] ?? [];
            $separatedMessages = [];

            foreach ($formattedMessages as $index => $msg) {
                $separatedMessages["Error-" . ($index + 1)] = $msg;
            }

            throw ValidationException::withMessages(['message' => $separatedMessages]);

        } catch (\Exception $e) {
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }
    }

    public function restartServiceModule(restartServiceModuleRequest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);
        $module = Module::find($credentials['module_id']);

        $command = 'systemctl restart ' . ModuleIdentity::serviceUnitName($module);

        $output = $this->runCommandModuleToServer(
            $credentials,
            $command,
            $server,
            'restartModel',
            'restartServiceModule'
        );

        return response()->json(['success' => true, 'message' => $output]);
    }

    public function startServiceModule(restartServiceModuleRequest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);
        $module = Module::find($credentials['module_id']);

        $command = 'systemctl start ' . ModuleIdentity::serviceUnitName($module);

        $output = $this->runCommandModuleToServer(
            $credentials,
            $command,
            $server,
            'startModule',
            'startServiceModule'
        );

        return response()->json(['success' => true, 'message' => $output]);
    }

    public function stopServiceModule(restartServiceModuleRequest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);
        $module = Module::find($credentials['module_id']);

        $command = 'systemctl stop ' . ModuleIdentity::serviceUnitName($module);

        $output = $this->runCommandModuleToServer(
            $credentials,
            $command,
            $server,
            'stopModule',
            'stopServiceModule'
        );

        return response()->json(['success' => true, 'message' => $output]);
    }

    public function statusServiceModule(restartServiceModuleRequest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);
        $module = Module::find($credentials['module_id']);

        $command = 'systemctl status ' . ModuleIdentity::serviceUnitName($module);

        $output = $this->runCommandModuleToServer(
            $credentials,
            $command,
            $server,
            'statusModule',
            'statusServiceModule'
        );

        return response()->json(['success' => true, 'message' => $output]);
    }

       public function PingServer(SshServerRequest $request)
    {
        $credentials = $request->validated();
        $server = Server::find($credentials['server_id']);
        
        $interface = $credentials['Interface'] ?? null;

        // بررسی می‌کنیم که اگر اینترفیس انتخاب شده Default بود، دستور بدون -I اجرا شود
        if (empty($interface) || strtolower(trim($interface)) === 'default') {
            $command = 'ping ' . $credentials['ipـdestination'] . ' -c 5';
        } else {
            $command = 'ping -I ' . $interface . ' ' . $credentials['ipـdestination'] . ' -c 5';
        }

        $port = isset($credentials['port']) ? (int) $credentials['port'] : 22;
        $sshHelper = new SshHelper($server->ip, $credentials['username'], $credentials['password'], $port);
        
        return response()->json([
            'success' => true, 
            'message' => $sshHelper->pingRunCommand($command)
        ]);
    }


}

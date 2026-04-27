<?php

namespace Modules\Server\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; 
use Modules\Server\Exceptions\CommandExecutionException;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Http\Requests\Module\restartServiceModuleRequest;
use Modules\Server\Http\Requests\SshServer\SshServerRequest;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
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
    ): string {
        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;
        $port = isset($credentials['port']) ? (int) $credentials['port'] : 22;

        if ($server->is_down == Server::OFF) {
            throw new CommandExecutionException(
                'server_off',
                "The selected server ({$server->name}) is offline.",
                ['server_id' => $server->id],
                422
            );
        }

        $sshHelper = new SshHelper($server->ip, $username, $password, $port, $timeout);
        return $sshHelper->runCommandModule($command, $typeCommand, $method);
    }

    private function serviceResponse(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (CommandExecutionException $exception) {
            return response()->json([
                'success' => false,
                'error_code' => $exception->errorCode,
                'message' => $exception->getMessage(),
                'details' => $exception->details,
            ], $exception->httpStatus);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'error_code' => 'validation_failed',
                'message' => 'Validation failed.',
                'details' => $exception->errors(),
            ], 422);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'error_code' => 'unexpected_remote_error',
                'message' => 'An unexpected remote command error occurred.',
                'details' => ['exception' => $exception->getMessage()],
            ], 500);
        }
    }

    public function restartServiceModule(restartServiceModuleRequest $request)
    {
        return $this->serviceResponse(function () use ($request) {
            $credentials = $request->validated();
            $server = Server::findOrFail($credentials['server_id']);
            $module = Module::findOrFail($credentials['module_id']);
            $command = 'systemctl restart ' . ModuleIdentity::serviceUnitName($module);

            $output = $this->runCommandModuleToServer(
                $credentials,
                $command,
                $server,
                'restartModel',
                'restartServiceModule'
            );

            return response()->json(['success' => true, 'message' => 'Service restart command executed successfully.', 'data' => ['output' => $output]]);
        });
    }

public function startServiceModule(restartServiceModuleRequest $request)
{
return $this->serviceResponse(function () use ($request) {

    $credentials = $request->validated();
    $server = Server::findOrFail($credentials['server_id']);
    $module = Module::findOrFail($credentials['module_id']);

    $service = ModuleIdentity::serviceUnitName($module);

    // 🔴 1. check if .conf exists (BLOCK start if true)
    $checkCommand = 'sudo test -f /etc/systemd/system/' . $service . '.conf && echo "CONF_EXISTS" || echo "NO_CONF"';

    $checkOutput = $this->runCommandModuleToServer(
        $credentials,
        $checkCommand,
        $server,
        'checkModule',
        'checkServiceConf'
    );

    Log::info('Check Output Type: ' . gettype($checkOutput));
    Log::info('Check Output Content: ', (array) $checkOutput);

    // تبدیل خروجی به رشته (در صورتی که آرایه یا آبجکت است، باید استخراج شود)
    // فرض می‌کنیم خروجی به صورت رشته متنی برمی‌گردد:
    $outputString = is_string($checkOutput) ? $checkOutput : json_encode($checkOutput);

    if (str_contains($outputString, 'CONF_EXISTS')) {
        return response()->json([
            'success' => false,
            'message' => 'Service is managed via .conf file and cannot be started directly.',
            'data' => [
                'output' => trim($outputString)
            ]
        ], 409);
    }
    // 🟢 2. start service only if safe
    $startCommand = 'systemctl start ' . $service;

    $output = $this->runCommandModuleToServer(
        $credentials,
        $startCommand,
        $server,
        'startModule',
        'startServiceModule'
    );

    return response()->json([
        'success' => true,
        'message' => 'Service start command executed successfully.',
        'data' => [
            'output' => $output
        ]
    ]);

});


}

    public function stopServiceModule(restartServiceModuleRequest $request)
    {
        return $this->serviceResponse(function () use ($request) {
            $credentials = $request->validated();
            $server = Server::findOrFail($credentials['server_id']);
            $module = Module::findOrFail($credentials['module_id']);
            $command = 'systemctl stop ' . ModuleIdentity::serviceUnitName($module);

            $output = $this->runCommandModuleToServer(
                $credentials,
                $command,
                $server,
                'stopModule',
                'stopServiceModule'
            );

            return response()->json(['success' => true, 'message' => 'Service stop command executed successfully.', 'data' => ['output' => $output]]);
        });
    }

    public function statusServiceModule(restartServiceModuleRequest $request)
    {
        return $this->serviceResponse(function () use ($request) {
            $credentials = $request->validated();
            $server = Server::findOrFail($credentials['server_id']);
            $module = Module::findOrFail($credentials['module_id']);
            $command = 'systemctl status ' . ModuleIdentity::serviceUnitName($module);

            $output = $this->runCommandModuleToServer(
                $credentials,
                $command,
                $server,
                'statusModule',
                'statusServiceModule'
            );

            return response()->json(['success' => true, 'message' => 'Service status command executed successfully.', 'data' => ['output' => $output]]);
        });
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

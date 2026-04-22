<?php

namespace Modules\Server\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
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
        $outputCommand = $sshHelper->runCommandModule($command, $typeCommand, $method);

        return $this->assertCommandOutputHasNoFailures($outputCommand, $command, $server);
    }

    private function assertCommandOutputHasNoFailures(string $rawOutput, string $command, Server $server): string
    {
        $rawOutput = (string) $rawOutput;
        $cleanOutput = preg_replace('/\x1b\[[0-9;?]*[a-zA-Z]/', '', $rawOutput) ?? $rawOutput;

        $exitCode = 0;
        if (preg_match('/__CMD_EXIT__:(\d+)/', $cleanOutput, $matches)) {
            $exitCode = (int) $matches[1];
            $cleanOutput = trim(preg_replace('/__CMD_EXIT__:\d+\s*$/', '', $cleanOutput) ?? $cleanOutput);
        }

        $lower = mb_strtolower($cleanOutput);

        if (str_contains($lower, 'sudo:') || str_contains($lower, 'a password is required') || str_contains($lower, 'is not in the sudoers file')) {
            throw new CommandExecutionException(
                'sudo_failed',
                'SSH connected, but sudo/systemctl execution failed.',
                ['command' => $command, 'output' => $cleanOutput, 'server_id' => $server->id]
            );
        }

        if (preg_match('/(unit\s+.+\s+could\s+not\s+be\s+found|not-found|loaded:\s+not-found)/i', $cleanOutput)) {
            throw new CommandExecutionException(
                'service_not_found',
                'Service unit was not found on the server.',
                ['command' => $command, 'output' => $cleanOutput, 'server_id' => $server->id]
            );
        }

        if ($exitCode !== 0) {
            throw new CommandExecutionException(
                'service_command_failed',
                'Service command failed on the remote server.',
                ['command' => $command, 'output' => $cleanOutput, 'exit_code' => $exitCode, 'server_id' => $server->id]
            );
        }

        return $cleanOutput;
    }

    private function serviceResponse(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (CommandExecutionException $exception) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $exception->errorCode,
                    'message' => $exception->getMessage(),
                    'details' => $exception->details,
                ],
            ], $exception->httpStatus);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'validation_failed',
                    'message' => 'Validation failed.',
                    'details' => $exception->errors(),
                ],
            ], 422);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'service_command_failed',
                    'message' => 'Service command failed on the remote server.',
                    'details' => ['exception' => $exception->getMessage()],
                ],
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

            return response()->json(['success' => true, 'data' => ['output' => $output]]);
        });
    }

    public function startServiceModule(restartServiceModuleRequest $request)
    {
        return $this->serviceResponse(function () use ($request) {
            $credentials = $request->validated();
            $server = Server::findOrFail($credentials['server_id']);
            $module = Module::findOrFail($credentials['module_id']);
            $command = 'systemctl start ' . ModuleIdentity::serviceUnitName($module);

            $output = $this->runCommandModuleToServer(
                $credentials,
                $command,
                $server,
                'startModule',
                'startServiceModule'
            );

            return response()->json(['success' => true, 'data' => ['output' => $output]]);
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

            return response()->json(['success' => true, 'data' => ['output' => $output]]);
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

            return response()->json(['success' => true, 'data' => ['output' => $output]]);
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

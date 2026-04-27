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
    }public function startServiceModule(restartServiceModuleRequest $request)
{
    return $this->serviceResponse(function () use ($request) {

        $credentials = $request->validated();
        $server = Server::findOrFail($credentials['server_id']);
        $module = Module::findOrFail($credentials['module_id']);

        $service = ModuleIdentity::serviceUnitName($module);

        // 🔴 0. Service name validation (anti injection)
        if (!preg_match('/^[a-zA-Z0-9@._-]+$/', $service)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service name.',
            ], 400);
        }

        // 🔴 1. Get systemd fragment path
        $fragmentCommand = "systemctl show " . escapeshellarg($service) . " --property=FragmentPath --value";

        $fragmentPath = trim((string)$this->runCommandModuleToServer(
            $credentials,
            $fragmentCommand,
            $server,
            'checkModule',
            'getFragmentPath'
        ));

        if (empty($fragmentPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        // 🔴 2. Block suspicious "=" (anti injection / broken output)
        if (str_contains($fragmentPath, '=')) {
            return response()->json([
                'success' => false,
                'message' => 'Blocked: invalid fragment output.',
                'data' => ['fragment_path' => $fragmentPath]
            ], 403);
        }

        // 🔴 3. ONLY blacklist .conf and override
        if (
            preg_match('/\.conf(\.|$)/', $fragmentPath) ||
            str_contains($fragmentPath, '.override')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Blocked: .conf / override services are forbidden.',
                'data' => ['fragment_path' => $fragmentPath]
            ], 403);
        }

        // 🔴 4. Resolve real path (anti symlink attack)
        $realPathCommand = "realpath " . escapeshellarg($fragmentPath);

        $realPath = trim((string)$this->runCommandModuleToServer(
            $credentials,
            $realPathCommand,
            $server,
            'checkModule',
            'resolveRealPath'
        ));

        if (empty($realPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot resolve service path.',
            ], 403);
        }

        // 🔴 5. Allowed base directories (system hardening)
        $allowedPaths = [
            '/etc/systemd/system/',
            '/lib/systemd/system/',
            '/usr/lib/systemd/system/',
            '/opt/',
        ];

        $allowed = false;
        foreach ($allowedPaths as $path) {
            if (str_starts_with($realPath, $path)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return response()->json([
                'success' => false,
                'message' => 'Blocked: path not allowed.',
                'data' => ['real_path' => $realPath]
            ], 403);
        }

        // 🔴 6. Final safety check (double guard)
        if (
            str_contains($realPath, '.conf') ||
            str_contains($realPath, '.override')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Blocked: unsafe unit detected.',
                'data' => ['real_path' => $realPath]
            ], 403);
        }

        // 🔴 7. Verify systemd actually knows this service
        $statusCommand = "systemctl status " . escapeshellarg($service) . " 2>&1";

        $statusOutput = $this->runCommandModuleToServer(
            $credentials,
            $statusCommand,
            $server,
            'checkModule',
            'verifyService'
        );

        if (str_contains($statusOutput, 'Loaded: not-found')) {
            return response()->json([
                'success' => false,
                'message' => 'Service not recognized by systemd.',
            ], 404);
        }

        // 🟢 8. START SERVICE (no whitelist restriction)
        $startCommand = 'systemctl start ' . escapeshellarg($service);

        $output = $this->runCommandModuleToServer(
            $credentials,
            $startCommand,
            $server,
            'startModule',
            'startService'
        );

        return response()->json([
            'success' => true,
            'message' => 'Service started successfully.',
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

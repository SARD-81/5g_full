<?php

namespace Modules\Server\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Modules\Server\Helpers\SshHelper;
use Modules\Server\Http\Requests\EditModuleRequest;
use Modules\Server\Http\Requests\Module\ExpertModuleFileIsServerRequset;
use Modules\Server\Http\Requests\Module\ShowConfigModuleRequest;
use Modules\Server\Http\Requests\Modules\CreateModulesRequest;
use Modules\Server\Http\Requests\Modules\deleteModuleRequest;
use Modules\Server\Http\Requests\Modules\ShowAllModules;
use Modules\Server\Http\Requests\Modules\ShowAllModulesRequest;
use Modules\Server\Http\Requests\Modules\ShowAllModulesRequestt;
use Modules\Server\Http\Requests\Modules\ShowAllServerModulesRequest;
use Modules\Server\Http\Requests\Modules\UpdateConfigModulerequest;
use Modules\Server\Http\Requests\Undo\UndoConfigModulesRequest;
use Modules\Server\Http\Requests\Undo\UndoToInitialConfigModulesRequest;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Modules\Server\Service\Parser\ModuleConfigParserService;
use Modules\Server\Service\Parser\YamlParserService;
use Modules\Server\Utility\CommandOutputAnalyzerService;
use Modules\Server\Utility\LogModuleService;
use Modules\Server\Utility\ModuleIdentity;

class ModuleController extends ApiController
{
    public function __construct()
    {}

    public function showConfigModule ($serverId, $moduleId)
    {
        $module = Module::where('id', $moduleId)
        ->whereHas('servers', function ($query) use ($serverId) {
            $query->where('server_id', $serverId);
        })
        ->with(['servers' => function ($query) {
            $query->select('servers.id', 'servers.name', 'servers.is_down');
        }])
        ->first();

        if (!$module)
            throw new HttpResponseException(response()->json(['msg' => 'The module with the provided ID was not found on the server you specified.']));



        $serverIdsInModuleName = $module->servers->pluck('pivot.server_id');
        $serversData = $module->servers->map(function ($server) {
            return [
                'id' => $server->id,
                'name' => $server->name,
                'is_down' => $server->is_down,
            ];
        });

        $currentConfig = $module->servers
        ->where('pivot.server_id', $serverId)
        ->first()?->pivot->current_config;


        return response()->json([
            'config' => json_decode($currentConfig),
            'serversDetails' => $serversData,
            'serversIdInModuleName' => $serverIdsInModuleName,
            'moduleDetails' => [
                'id' => $module['id'],
                'name' => $module['name'],
                'type' => $module['type']
            ]
        ]);
    }
    public function showAllServiseAndModulesInServer (ShowAllServerModulesRequest $request, $serverId)
    {
        $credentials = $request->validated();

        if ((int) $credentials['server_id'] !== (int) $serverId) {
            throw ValidationException::withMessages(['server_id' => 'The submitted server_id does not match route serverID.']);
        }

        $server = Server::with(['modules:id,name,type,service_key'])->find($serverId);
        if (! $server) {
            return response()->json(['msg' => 'invalide server id'], 404);
        }

        $this->chackPermissionModule($server);

        if ($server['is_down'] == Server::OFF) {
            throw ValidationException::withMessages(['server.down' => 'this server: ' . $server['name'] . ' is off']);
        }

        $sshHelper = new SshHelper(
            $server->ip,
            $credentials['username'],
            $credentials['password'],
            $credentials['port'] ?? 22
        );

        $existingModules = $server->modules->filter(function ($module) use ($sshHelper, $server) {
            $configPath = rtrim((string) $server->path_config, '/') . '/' . ModuleIdentity::configFileName($module);
            $checkCommand = 'test -f ' . escapeshellarg($configPath);
            $output = $sshHelper->runCommandModule($checkCommand, 'show-server-module-list', 'showAllServiseAndModulesInServer');

            return str_contains($output, "");
        })->values();


        $modulesGroupedByType = collect();
        foreach ($existingModules as $module) {
            $types = array_map('trim', explode(',', $module->type));

            foreach ($types as $type) {
                if (!$modulesGroupedByType->has($type))
                    $modulesGroupedByType->put($type, collect());
                $modulesGroupedByType->get($type)->push($module);
            }
        }

        $response = [
            'Epc' => $modulesGroupedByType->get('Epc', []),
            '5gc' => $modulesGroupedByType->get('5gc', []),
            'allModules' => $existingModules->makeHidden('pivot')
        ];

        return $this->respondSuccess('List of server services and their modules', $response);
    }
    public function ShowAllModules (Request $request)
    {
        $user = Auth::user();
        $perPage = ($request->input('paginate') ?? 10);

        if ($user->hasRole('admin')) {
            return response()->json([
                'msg' => 'The list of modules was successfully retrieved',
                'module' => $this->formatModules(Module::with('servers')->paginate($perPage))
            ]);
        }

        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();



        $modules = Module::whereHas('servers', function ($query) use ($userPermissions) {
            $query->whereIn('name', collect($userPermissions)->map(function ($permission) {
                return str_replace('server/', '', $permission);
            })->toArray());
        })->with(['servers' => function ($query) use ($userPermissions) {
            $query->whereIn('name', collect($userPermissions)->map(function ($permission) {
                return str_replace('server/', '', $permission);
            })->toArray());
        }])->paginate($perPage);


        return response()->json([
            'msg' => 'The list of modules was successfully retrieved',
            'module' => $this->formatModules($modules)
        ]);
    }
    private function formatModules($modules)
    {
      $paginationData = [
          'current_page' => $modules->currentPage(),
          'per_page' => $modules->perPage(),
          'total' => $modules->total(),
          'last_page' => $modules->lastPage(),
      ];

      $userPermissions = Auth::user()->getAllPermissions()
            ->filter(fn($permission) => str_starts_with($permission->name, 'server'))
            ->pluck('name')
            ->toArray();


      $formattedModules = $modules->getCollection()->map(function ($module) {
          return [
              'module_id' => $module->id,
              'module_name' => $module->name,
              'module_type' => $module->type,
              
              'server_detaile' => $module->servers->map(fn($server) => [
                  'id' => $server->id,
                  'name' => $server->name,
                  'is_down' => $server->is_down,
              ]),
              'server_name' => $module->servers->pluck('name')->toArray(),
              'server_ids' => $module->servers->pluck('id')->toArray(),
          ];
      });

      return [
          'msg' => 'The list of modules was successfully retrieved',
          'user_permissions_server' => $userPermissions,
          'module' => $formattedModules,
          'pagination' => $paginationData,
      ];
    }


    public function createModule (CreateModulesRequest $request)
    {
        $credential  = $request->validated();
        $serverIds   = array_column($credential['servers'], 'id');
        $parsedConfig = ModuleConfigParserService::parseUploadedFile($request->file('config_file'));
        $jsonContent = $parsedConfig['stored_json'];
        $configType = $request->input('configType');
        $yamlContent = $parsedConfig['remote_content'];

        $failedServers  = [];
        $createdModules = [];


        try {
            DB::beginTransaction();

            $module = Module::create([
                'name' => $credential['name'],
                'type' => $credential['type'],
            ]);


            foreach ($serverIds as $serverId) {
                $server      = Server::find($serverId);
                $serverIndex = array_search($serverId, array_column($credential['servers'], 'id'));
                $username    = $credential['servers'][$serverIndex]['username'];
                $password    = $credential['servers'][$serverIndex]['password'];
                $port        = $credential['servers'][$serverIndex]['port'] ?? 22;

                    // check permission
                $this->chackPermissionModule($server);

                if (!$server) $failedServers[] = $serverId;

                if ($server && $server['is_down'] == Server::OFF) return response()->json(['msg'=> 'server is off', 'server' => $server], 422);

                    $module->servers()->syncWithoutDetaching([
                        $serverId => [
                            'current_config' => $jsonContent,
                            'initial_config' => $jsonContent
                        ]
                    ]);

                $outputCommand = $this->sendConfigToServer(
                    $username,
                    $password,
                    $credential['name'],
                    $yamlContent,
                    $server,
                    'create-module',
                    'createModule',
                    $port ?? 22,
                );


                $commandWarning = ! empty($outputCommand) ? CommandOutputAnalyzerService::extractErrors($outputCommand) : null;
                if ($commandWarning) throw ValidationException::withMessages($commandWarning);

                 $module->servers()->syncWithoutDetaching([$serverId]);

                $createdModules[] = [
                    'server' => [
                        'server_id'   => $server['id'],
                        'server_name' => $server['name'],
                        'server_ip'   => $server['ip']
                    ],
                    'module' => [
                        'module_id'   => $module['id'],
                        'module_name' => $module['name'],
                        'config_type' => $configType,
                        'module_type' => $module['type']
                    ]
                ];

                activity('create-module')
                    ->causedBy(Auth::user())
                    ->performedOn(Module::latest()->first())
                    ->event('create-module')
                    ->withProperties([
                        'type-log' => 'server',
                        'route' => request()->fullUrl(),
                        'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                        'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                        'method' => 'createModule',
                        'module' => [
                            'name' => $credential['name'],
                            'type' => $credential['type'],
                            'server_id' => $serverId,
                        ],
                        'server' => $server
                    ])
                    ->log('A new module has been created');
            }

            if (!empty($failedServers)) return response()->json(['msg' => 'An issue occurred while adding the module to the server', 'server-faild' => $failedServers], 422);

            DB::commit();

            return response()->json([
                'success' => $commandWarning ? false : true,
                'msg' => $commandWarning ? 'Failed to create the module on the servers' : 'The module was successfully created on the servers',
                'data' => ['created_modules' => $createdModules]
            ], $commandWarning ? 422 : 200);

        } catch (Exception $e) {
            DB::rollBack();
                throw $e;
        }
    }
    public function deleteModule (deleteModuleRequest $request)
    {
        $credentials = $request->validated();
        $module = Module::with('servers')->findOrFail($credentials['module_id']);

        try {
            $attachedServers = $module->servers;
            $submittedServers = collect($credentials['servers'] ?? [])->keyBy(
                fn ($item) => (int) $item['id']
            );

            foreach ($attachedServers as $server) {
                $this->chackPermissionModule($server);

                $serverCredentials = $submittedServers->get((int) $server->id);
                if (!$serverCredentials) {
                    throw ValidationException::withMessages([
                        'servers' => "SSH credentials for server ID {$server->id} are required.",
                    ]);
                }

                $this->cleanupModuleArtifactsFromServer($module, $server, $serverCredentials);
            }

            DB::beginTransaction();
            $module->delete();
            DB::commit();

            activity('delete-module')
                ->causedBy(Auth::user())
                ->event('delete-module')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' => Auth::user()->roles()->pluck('name')->first(),
                    'method' => 'deleteModule',
                    'module' => [
                        'name' => $module['name'],
                        'type' => $module['type'],
                        'server_id' => $attachedServers->pluck('id')->toArray(),
                    ],
                ])
                ->log('The module has been deleted successfully after remote cleanup.');

            return response()->json(['msg' => 'Module Deleted', 'module' => $module]);

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
                return response()->json(['success' => false, 'msg' => $e->getMessage()], 422);
        }
    }

    private function cleanupModuleArtifactsFromServer(Module $module, Server $server, array $credentials): void
    {
        if (empty($server->path_config) || empty($server->path_run_config)) {
            throw ValidationException::withMessages([
                'server' => "Server {$server->name} is missing required paths for module cleanup.",
            ]);
        }

        $username = (string) $credentials['username'];
        $password = (string) $credentials['password'];
        $port = (int) ($credentials['port'] ?? 22);

        $pathConfig = rtrim((string) $server->path_config, '/');
        $pathRunConfig = rtrim((string) $server->path_run_config, '/');

        $identityConfig = $pathConfig . '/' . ModuleIdentity::configFileName($module);
        $legacyConfig = $pathConfig . '/' . $module->name . '.yaml';
        $serviceName = ModuleIdentity::serviceUnitName($module);

        $runArtifacts = [
            $pathRunConfig . '/' . $serviceName,
            $pathRunConfig . '/' . $serviceName . '.service',
            $pathRunConfig . '/run-' . $module->service_key,
            $pathRunConfig . '/run-' . $module->name,
            $pathRunConfig . '/' . $module->service_key . '.yaml',
            $pathRunConfig . '/' . $module->name . '.yaml',
        ];

        $artifactPaths = array_values(array_unique(array_filter(array_merge([
            $identityConfig,
            $legacyConfig,
        ], $runArtifacts))));

        $checks = implode(' && ', array_map(
            fn ($path) => 'test ! -e ' . escapeshellarg($path),
            $artifactPaths
        ));

        $command = implode(' ; ', [
            'systemctl stop ' . escapeshellarg($serviceName) . ' >/dev/null 2>&1 || true',
            'systemctl disable ' . escapeshellarg($serviceName) . ' >/dev/null 2>&1 || true',
            'rm -f ' . implode(' ', array_map(fn ($path) => escapeshellarg($path), $artifactPaths)) . ' || exit 127',
            '(' . $checks . ') || exit 127',
        ]);

        $sshHelper = new SshHelper($server->ip, $username, $password, $port);
        $sshHelper->runCommandModule($command, 'delete-module-cleanup', 'deleteModule.cleanupModuleArtifactsFromServer');
    }



        // update Config Module
    private function sendConfigToServer(
        string $username,
        string $password,
        string $moduleName,
        string $yamlContent,
        Server $server,
        string $typeCommand,
        string $method,
        int $port = 22,
    ) {
        if ($server['is_down'] == Server::OFF) throw ValidationException::withMessages(['server.down' => 'this server: ' . $server['name'] .' is off']);

        if (!$server['path_config']) throw ValidationException::withMessages(['server.path_config' => 'You did not specify a configuration address config']);

        if (!$server['path_run_config']) throw ValidationException::withMessages(['server.path_run_config' => 'You did not specify a configuration address run config']);


        $sshHelper = new sshHelper($server->ip, $username, $password, $port);

        // update module
        $commandUpdateFileModule = 'echo ' . escapeshellarg($yamlContent) . ' > ' . $server['path_config'] . $moduleName . '.yaml';
        return $sshHelper->runCommandModule($commandUpdateFileModule, $typeCommand, $method, $server);

        // restart module
//        $commandRestart = $server['path_run_config'] . 'bbdh-' . $moduleName . 'd' . ' restart';
        // $output = $sshHelper->restartModule($commandRestart );

    }
    public function chackPermissionModule($server)
    {
        $user = Auth::user();
        if ($user->hasRole('admin'))
            return true;


        $serverPermission = 'server/' . $server['name'];
        if ($user->hasPermissionTo($serverPermission))
            return true;

        throw ValidationException::withMessages([
            'msg' => 'You do not have permission to use this server : ' . $server['name'],
            'your-permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }
    private function updateMultipleModules2(array $serverIds, Request $request)
    {
        $module = Module::find($request['module_id']);

        $serverIdsInModuleName = $module->servers->pluck('id');
        foreach ($serverIds as $serverId)
            if (!in_array($serverId, $serverIdsInModuleName->toArray())) throw ValidationException::withMessages(['module' => 'An invalid server ID has been sent among the server IDs']);


        DB::beginTransaction();
        try {
            foreach ($serverIds as $serverId) {

                $server      = Server::find($serverId);
                $serverIndex = array_search($serverId, array_column($request['servers'], 'id'));
                $username    = $request['servers'][$serverIndex]['username'];
                $password    = $request['servers'][$serverIndex]['password'];
                $port        = $request['servers'][$serverIndex]['port'] ?? 22;

                $module = $server->modules()->wherePivot('module_id', $request['module_id'])->first();

                $this->chackPermissionModule($module, $server);

                $updatedModule = $this->updateModuleConfigInDatabase($module['id'], $request->input('data'), $server);

                $yamlContent = YamlParserService::convertJsonToYaml($updatedModule);

                $outputCommand = $this->sendConfigToServer(
                    $username,
                    $password,
                    $module['name'],
                    $yamlContent,
                    $server,
                    'update-module',
                    'updateMultipleModules',
                    $port,
                );


                $commandWarning = ! empty($outputCommand) ? CommandOutputAnalyzerService::extractErrors($outputCommand) : null;
                if ($commandWarning) throw ValidationException::withMessages($commandWarning);


                LogModuleService::logModuleUpdate($module, $server,  $request->input('data'));
            }

            DB::commit();


            $serversData = $module->servers->map(function ($server) {
                return [
                    'id' => $server->id,
                    'name' => $server->name,
                    'is_down' => $server->is_down,
                ];
            });

            return response()->json([
                'config' => json_decode($updatedModule, true),
                'commandWarinig' => $commandWarning,
                'serverDetaile' => $serversData,
                'serverIdsInModuleName' => $serverIdsInModuleName
            ], $commandWarning ? 422 : 200);

        } catch (HttpResponseException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
                DB::rollBack();

                $message = $e->getMessage();
                $message = preg_replace('/\x1b\[[0-9;]*m/', '', $message); // حذف کدهای ANSI
                $message = preg_replace('/\r?\n.*?\[root@localhost.*?$/', '', $message); // حذف اطلاعات اضافی مربوط به خط فرمان

                preg_match_all('/\b(FATAL|ERROR):\s.*?(?=\s\(.*?\)|$)/m', $message, $matches);

                $formattedMessages = $matches[0] ?? [];

                $separatedMessages = [];
                foreach ($formattedMessages as $index => $msg) {
                    $separatedMessages["Error-" . ($index + 1)] = $msg;
                }

            throw ValidationException::withMessages(['server-warning' => $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollBack();

            throw ValidationException::withMessages(['server-warning' => $e->getMessage()]);
        }
    }

    private function updateMultipleModules(array $serverIds, Request $request)
    {
        $module = Module::find($request['module_id']);
        $serverIdsInModuleName = $module->servers->pluck('id');

        // 1. Validation
        foreach ($serverIds as $serverId) {
            if (!in_array($serverId, $serverIdsInModuleName->toArray())) {
                throw ValidationException::withMessages(['module' => 'An invalid server ID has been sent among the server IDs']);
            }
        }

        $updatedModuleJson = null;
        $commandWarning = null;

        try {
            foreach ($serverIds as $serverId) {
                $server = Server::find($serverId);
                $serverIndex = array_search($serverId, array_column($request['servers'], 'id'));
                $username    = $request['servers'][$serverIndex]['username'];
                $password    = $request['servers'][$serverIndex]['password'];
                $port        = $request['servers'][$serverIndex]['port'] ?? 22;

                // پیدا کردن پیوت ماژول
                $modulePivot = $server->modules()->wherePivot('module_id', $request['module_id'])->first();
                $this->chackPermissionModule($modulePivot, $server);

                $data = $request->input('data');
                $normalizedPayload = ModuleConfigParserService::normalizeUpdatePayload(
                    $data,
                    $modulePivot->pivot->current_config
                );

                $jsonContent = $normalizedPayload['stored_json'];
                $yamlContent = $normalizedPayload['remote_content'];
                // --- STEP 2: SSH Operation (Heavy Lift - No DB Lock) ---
                // ارسال به سرور قبل از درگیر کردن دیتابیس
                $outputCommand = $this->sendConfigToServer(
                    $username,
                    $password,
                    $modulePivot['name'],
                    $yamlContent,
                    $server,
                    'update-module',
                    'updateMultipleModules',
                    $port,
                );

                // بررسی خطا
                $commandWarning = ! empty($outputCommand) ? CommandOutputAnalyzerService::extractErrors($outputCommand) : null;
                if ($commandWarning) {
                    // اگر SSH خطا داد، دیتابیس آپدیت نمی‌شود و اکسپشن پرتاب می‌شود
                    throw ValidationException::withMessages($commandWarning);
                }

                // --- STEP 3: Database Update (Fast & Atomic) ---
                // حالا که مطمئن شدیم کانفیگ روی سرور نشسته، دیتابیس را آپدیت می‌کنیم
                // از تراکنش استفاده می‌کنیم تا آپدیت و لاگ اتمیک باشند
                DB::transaction(function () use ($modulePivot, $server, $jsonContent, $normalizedPayload) {
                    $serverModel = $modulePivot->servers()->find($server['id']);
                    $moduleCurrentConfig = $serverModel->pivot['current_config'];

                    $modulePivot->servers()->updateExistingPivot($server['id'], [
                        'current_config' => $jsonContent,
                        'previous_config' => $moduleCurrentConfig,
                    ]);

                    LogModuleService::logModuleUpdate($modulePivot, $server, $normalizedPayload['editor_data']);
                });

                // نگه داشتن آخرین کانفیگ برای خروجی
                $updatedModuleJson = json_encode($normalizedPayload['editor_data'], JSON_PRETTY_PRINT);
            }

            // --- Response Preparation ---
            $serversData = $module->servers->map(function ($server) {
                return [
                    'id' => $server->id,
                    'name' => $server->name,
                    'is_down' => $server->is_down,
                ];
            });

            return response()->json([
                'config' => json_decode($updatedModuleJson, true),
                'commandWarinig' => $commandWarning,
                'serverDetaile' => $serversData,
                'serverIdsInModuleName' => $serverIdsInModuleName,
                'yamlContent' => $yamlContent,
            ], 200);

        } catch (HttpResponseException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            // نیازی به DB::rollback نیست چون تراکنش ما فقط در اسکوپ کوچک مرحله 3 بود
            // و اگر آنجا خطا دهد خودش رول‌بک می‌شود.

            $message = $e->getMessage();
            $message = preg_replace('/\x1b\[[0-9;]*m/', '', $message);
            $message = preg_replace('/\r?\n.*?\[root@localhost.*?$/', '', $message);

            preg_match_all('/\b(FATAL|ERROR):\s.*?(?=\s\(.*?\)|$)/m', $message, $matches);

            // منطق فرمت کردن مسیج‌ها...
            // (کد شما در اینجا تغییری نکرده فقط Rollback حذف شده چون تراکنش سراسری نداریم)

            throw ValidationException::withMessages(['server-warning' => $e->getMessage()]);
        } catch (\Exception $e) {
            // مدیریت خطاهای عمومی
            throw ValidationException::withMessages(['server-warning' => $e->getMessage()]);
        }
    }
    private function updateModuleConfigInDatabase($moduleId, $data, $server)
    {
        if ($server['is_down'] == Server::OFF)
            throw ValidationException::withMessages(['server' => 'server is off']);

        $module = Module::find($moduleId);
        if (!$module)
            throw ValidationException::withMessages(['module' => 'module not found']);

            // example value in data user
        foreach ($data as $key => $value) {
            if (is_null($value))
                $data[$key] = "";
        }

        $serverModel = $module->servers()->find($server['id']);

        $moduleCurrentConfig = $serverModel->pivot['current_config'];
        $serverModel->pivot['previous_config'] = $moduleCurrentConfig;

        $module->servers()->updateExistingPivot($server->id, [
            'current_config' => json_encode($data, JSON_PRETTY_PRINT),
            'previous_config' => $moduleCurrentConfig
        ]);


        return json_encode($data);
    }
    public function updateConfigModule(UpdateConfigModuleRequest $request)
    {
        $credentials = $request->validated();
        $serverIds   = array_column($credentials['servers'], 'id');

        try {

            return $this->updateMultipleModules($serverIds, $request);

        } catch (\Exception $e) {
            throw $e;
        }
    }



        // edit config module
    private function updateConfigForDB(Module $module, array $serverIds, $jsonConfig, Request $request, int $port)
    {
        $moduleConfig = json_decode($jsonConfig, true);
        $encodedConfig = json_encode($moduleConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        foreach ($serverIds as $serverId) {

            $server       = Server::find($serverId);
            $serverModule = $module->servers()->where('server_id', $serverId)->first();

            $serverIndex  = array_search($server['id'], array_column($request['servers'], 'id'));
            $username     = $request['servers'][$serverIndex]['username'];
            $password     = $request['servers'][$serverIndex]['password'];
            $port         = $request['servers'][$serverIndex]['port'] ?? 22;


            if ($serverModule) {

                $pivotData = $serverModule->pivot;
                $pivotData->previous_config = $pivotData->current_config;
                $pivotData->initial_config = $encodedConfig;
                $pivotData->current_config = $encodedConfig;

                $yamlContent = ModuleConfigParserService::serializeForRemote($pivotData->current_config);
                $outputCommand = $this->sendConfigToServer(
                    $username,
                    $password,
                    $module->name,
                    $yamlContent,
                    $server,
                    'substitute-module-config-file',
                    'sendDefaultConfigToServers',
                    $port
                );

                $pivotData->save();

                $commandWarning = ! empty($output) ? CommandOutputAnalyzerService::extractErrors($output) : null;
                if ($commandWarning) throw ValidationException::withMessages($commandWarning);


//                $cleanCommand = preg_replace('/\e\[[\d;?]*[a-zA-Z]/', '', $outputCommand);

//                if (! empty($cleanCommand)) {
//                    throw ValidationException::withMessages(['commandWarning' => $cleanCommand]);
//                }

                return $outputCommand;
            }
        }
        return null;
    }
    private function sendDefaultConfigToServers(array $serverIds, Request $request, Module $module, int $port)
    {
        $parsedConfig = ModuleConfigParserService::parseUploadedFile($request->file('config_file'));
        $jsonContent = $parsedConfig['stored_json'];

        foreach ($serverIds as $serverId) {

            $server      = Server::find($serverId);
            $serverIndex = array_search($server['id'], array_column($request['servers'], 'id'));
            $username    = $request['servers'][$serverIndex]['username'];
            $password    = $request['servers'][$serverIndex]['password'];
            $port        = $request['servers'][$serverIndex]['port'] ?? 22;


            $defaultConfig = [
                'initial_config' => $jsonContent,
                'current_config' => $jsonContent,
            ];

            $module->servers()->attach($serverId,$defaultConfig);

            $yamlContent = ModuleConfigParserService::serializeForRemote($defaultConfig['initial_config']);
            $outputCommand = $this->sendConfigToServer(
                $username,
                $password,
                'default_module',
                $yamlContent,
                $server,
                'substitute-module-config-file',
                'sendDefaultConfigToServers',
                $port,
            );

            if (! empty(CommandOutputAnalyzerService::extractErrors($outputCommand)))
                throw ValidationException::withMessages(CommandOutputAnalyzerService::extractErrors($outputCommand));

        }
    }
    private function addModules(Module $module, array $serverIds, Request $request, int $port)
    {
        if ($module->servers->isEmpty()) {
            $this->sendDefaultConfigToServers($serverIds, $request, $module, $port);
            return;
        }

      $pivotData = $module->servers()->first()->pivot;

        foreach ($serverIds as $serverId) {

            $server      = Server::find($serverId);
            $serverIndex = array_search($server['id'], array_column($request['servers'], 'id'));
            $username    = $request['servers'][$serverIndex]['username'];
            $password    = $request['servers'][$serverIndex]['password'];
            $port        = $request['servers'][$serverIndex]['port'] ?? 22;


            if (!$module->servers->contains($serverId)) {
                $module->servers()->attach($serverId, [
                    'initial_config' => $pivotData->initial_config,
                    'current_config' => $pivotData->initial_config,
                ]);
            }

            $yamlContent = ModuleConfigParserService::serializeForRemote($pivotData['initial_config']);
            $this->sendConfigToServer(
                $username,
                $password,
                $module['name'],
                $yamlContent,
                $server,
                'add-module-to-selected-server',
                'editModule.addModuletoSelectedServer',
                $port,
            );

        }
    }
    private function deleteModules(array $serverIds, Module $module)
    {
        foreach ($serverIds as $serverId) {
                $module->servers()->detach($serverId);
        }
    }
    private function syncModuleWithServers(Module $module, array $serverIds, $request, int $port)
    {
        $existingServerIds = $module->servers->pluck('id')->toArray();

        $serversToDelete = array_diff($existingServerIds, $serverIds);
        $serversToAdd = array_diff($serverIds, $existingServerIds);


        $this->addModules($module, $serversToAdd, $request, $port);
        $this->deleteModules($serversToDelete, $module, $request, $port);
    }
    public function editModule(EditModuleRequest $request)
    {
        $credentials = $request->validated();


        try {
            DB::beginTransaction();

            $module = Module::find($credentials['module_id']);
            $oldModule = clone $module;
            $hasServersInput = array_key_exists('servers', $credentials);
            $serverIds = array_column($credentials['servers'] ?? [], 'id');
            $configFile = $request->file('config_file');

            if ($module->servers->isEmpty() && !$configFile) throw ValidationException::withMessages(['config' => 'config file required']);

            if ($hasServersInput) {
                // check permissions
                foreach ($serverIds as $serverId) {
                    $server = Server::find($serverId);
                    $this->chackPermissionModule($server);

                    if ($server['is_down'] === Server::OFF) throw ValidationException::withMessages(['msg' => 'server : ' . $server['name'] . ' is off']);
                }

                $this->syncModuleWithServers($module, $serverIds, $request, $credentials['port'] ?? 22);

                // update file
                if ($configFile) {
                    $jsonConfig = ModuleConfigParserService::parseUploadedFile($configFile)['stored_json'];
                    $output = $this->updateConfigForDB($module, $serverIds, $jsonConfig, $request, $credentials['port'] ?? 22);
                }
            }



                $types = implode(',', array_map('trim', explode(',', $credentials['type'] ?? $module['type'])));

                $module->update([
                    'name' => $credentials['name'] ?? $module->name,
                    'type' => $types,
                ]);

                $module->load('servers');


                activity('edit-module')
                    ->causedBy(Auth::user())
                    ->performedOn($module)
                    ->event('update')
                    ->withProperties([
                        'type-log' => 'server',
                        'route' => request()->fullUrl(),
                        'method' => 'editModule',
                        'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                        'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                        'old_module_data' => [
                            'id' => $oldModule->id,
                            'name' => $oldModule->name,
                            'type' => $oldModule->type,
                        ],
                        'new_module_data' => [
                            'id' => $module->id,
                            'name' => $module->name,
                            'type' => $module->type,
                        ],
                        'module_server' => optional($module->servers->first(), function ($server) {
                            return [
                                'id' => $server->id,
                                'name' => $server->name,
                                'path_config' => $server->path_config,
                                'path_fun_config' => $server->path_fun_config,
                                'ip' => $server->ip,
                            ];
                        })
                    ])
                    ->log('The export file config is taken from the module configuration.');



            DB::commit();
                return response()->json([
                    'message' => 'Module updated successfully',
                    'output' => $output ?? null,
                    'module' => [
                        'module_id' => $module['id'],
                        'module_name' => $module['name'],
                        'module_type' => $module['type'],
                        'module_server' => $module->servers->pluck('id')->toArray(),
                        'module_server_name' => $module->servers->pluck('name')->toArray(),
                    ]
                ], 200);

        } catch (\Exception $e){
            DB::rollBack();
                throw $e;
        }
    }


    public function expertModuleFileIsServer (ExpertModuleFileIsServerRequset $request)
    {
        $credentials = $request->validated();
        $module = Module::find($credentials['module_id']);
        $server = Server::find($credentials['server_id']);


        $command = 'cat ' . $server->path_config . $module->name . '.yaml' ;

        if ($server['is_down'] == server::OFF)
            throw ValidationException::withMessages(['server' => 'server is off']);

        try {
                // download file
            $sshHelper = new sshHelper($server->ip, $credentials['username'], $credentials['password'], $credentials['port'] ?? 22);
            $output = $sshHelper->getFileContent($command);

            $commandWarning = ! empty($output) ? CommandOutputAnalyzerService::extractErrors($output) : null;
            if ($commandWarning) throw ValidationException::withMessages($commandWarning);


            activity('export-config-module')
                ->causedBy(Auth::user())
                ->event('get')
                ->withProperties([
                    'type-log'    => 'server',
                    'route'       => request()->fullUrl(),
                    'method'      => 'expertModuleFileIsServer',
                    'user'        => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role'   => Auth::user()->roles()->pluck('name')->first(),
                    'module_id'   => $module['id'],
                    'module_name' => $module['name'],
                    'module_type' => $module['type'],
                    'server'      => $server
                ])
                ->log('The export file config is taken from the module configuration.');


            return response($output, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => "attachment; filename={$module->name}.yaml",
                'X-Name-Header' => "{$module->name}.yaml",
                'Content-Length' => strlen($output),
            ]);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function undoConfigModule (UndoConfigModulesRequest $request)
    {
        $credential           = $request->validated();
        $pivotData            = $request['module']->servers()->where('server_id', $credential['server_id'])->first()->pivot;
        $modulePreviousConfig = $pivotData['previous_config'];

        if ($modulePreviousConfig == null) throw ValidationException::withMessages(['module' => 'The module does not have a previous value, you cannot revert it to the previous value']);

        try {
            // ssh to server format yaml
            $yamlContent = ModuleConfigParserService::serializeForRemote($pivotData['previous_config']);
            $outputCommand = $this->sendConfigToServer(
                $credential['username'],
                $credential['password'],
                $request['module']['name'],
                $yamlContent, $request['server'],
                'undo-config-module',
                'undoConfigModule',
                $credential['port'] ?? 22,
            );


            // save to datebase format json
            $pivotData['current_config'] = $pivotData['previous_config'];
            $pivotData->save();


            $commandWarning = ! empty($outputCommand) ? CommandOutputAnalyzerService::extractErrors($outputCommand) : null;
            if ($commandWarning) throw ValidationException::withMessages($commandWarning);


            activity('undo-config-module')
                ->causedBy(Auth::user())
                ->performedOn($request['module'])
                ->event('undo-config-module')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'undoConfigModule',
                    'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' => Auth::user()->roles()->pluck('name')->first(),
                    'module_id' => $request['module']['id'],
                    'module_name' => $request['module']['name'],
                    'module_type' => $request['module']['type'],
                    'server_id' => $request['server']?->id
                ])
                ->log('The module configuration has been reverted to the previous step');


            return response()->json([
                'success' => $commandWarning ? false : true,
                'msg' => 'The module configuration has been reverted to the previous step',
                'commandWarning' => $commandWarning,
                'config' => json_decode($pivotData['current_config'], true)
            ], $commandWarning ? 422 : 200);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 422);
        }
    }
    public function undoToInitialConfigModule (UndoToInitialConfigModulesRequest $request)
    {
        $credential          = $request->validated();
        $pivotData           = $request['module']->servers()->where('server_id', $credential['server_id'])->first()->pivot;
        $moduleInitialConfig = $pivotData['initial_config'];

        try {
            DB::beginTransaction();

                    // ssh to server format yaml
            $yamlContent = ModuleConfigParserService::serializeForRemote($moduleInitialConfig);

            $outputCommand = $this->sendConfigToServer(
                $credential['username'],
                $credential['password'],
                $request['module']['name'],
                $yamlContent, $request['server'],
                'undo-initial-config-module',
                'undoToInitialConfigModule',
                $credential['port'] ?? 22,
            );


            // save to datebase format json
            $pivotData['current_config'] = $pivotData['initial_config'];
            $pivotData->save();


            $commandWarning = ! empty($outputCommand) ? CommandOutputAnalyzerService::extractErrors($outputCommand) : null;
            if ($commandWarning) throw ValidationException::withMessages($commandWarning);


            activity('undo-config-module')
                ->causedBy(Auth::user())
                ->performedOn($request['module'])
                ->event('undo-config-module')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'undoConfigModule',
                    'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                    'module_id' => $request['module']['id'],
                    'module_name' => $request['module']['name'],
                    'module_type' => $request['module']['type'],
                    'server' => $request['server']
                ])
            ->log('The module configuration has been reverted to its initial state');

            DB::commit();
                return response()->json([
                    'success' => $commandWarning ? false : true,
                    'msg' => 'The module configuration has been reverted to its initial state',
                    'commandWarning' => $commandWarning,
                    'config' => json_decode($pivotData['initial_config'], true)
                ], $commandWarning ? 422 : 200);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
                return response()->json(['error'=> $e->getMessage()], 422);
        }
    }

}

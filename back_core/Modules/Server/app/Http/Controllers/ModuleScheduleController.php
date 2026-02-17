<?php

namespace Modules\Server\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Backup\Services\CronTabService;
use Modules\Server\Http\Requests\Modules\CreateModuleScheduleRequest;
use Modules\Server\Http\Requests\Modules\UpdateModuleScheduleRequest;
use Modules\Server\Models\ModuleSchedule;
use Modules\Server\Models\Server;
use Modules\Server\Service\Parser\YamlParserService;

class ModuleScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ModuleSchedule::cursor()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateModuleScheduleRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $serverIds   = array_column($credentials['servers'], 'id');


//        if (env('SSH_STATUS')) {
            $cronTabService = new CronTabService($credentials['password']);
            $cronTabService->set();
//        }
        $moduleSchedules = [];
        try {
            DB::beginTransaction();

            foreach ($serverIds as $serverId) {
                $server      = Server::find($serverId);
                $serverIndex = array_search($serverId, array_column($credentials['servers'], 'id'));
                $username    = $credentials['servers'][$serverIndex]['username'];
                $password    = $credentials['servers'][$serverIndex]['password'];
                $port        = $credentials['servers'][$serverIndex]['port'] ?? 22;

                $moduleSchedules[] = ModuleSchedule::create([
                    'config'           => $request->file('config_file')->getContent(),
                    'status'           => ModuleSchedule::WAITING,
                    'module_id'        => $credentials['module_id'],
                    'server_id'        => $server['id'],
                    'run_scheduled_at' => $credentials['run_scheduled_at'],
                    'username_ssh'     => $username,
                    'password_ssh'     => $password,
                    'port_ssh'         => $port ?? 22,
                ]);
            }

            DB::commit();
                return response()->json(['success' => true, 'msg' => 'create module schedule successful', 'data' => $moduleSchedules]);

        } catch (\Exception $e) {
            DB::rollBack();
                throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, UpdateModuleScheduleRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        try {
            DB::beginTransaction();

                $updateModuleSchedule = tap($request->moduleSchedule)->update([

                    'config'           => isset($credentials['config_file'])
                        ? $request->file('config_file')->getContent()
                        : $request->moduleSchedule->config,

                    'module_id'        => $credentials['module_id']        ?? $request->moduleSchedule->module_id,
                    'server_id'        => $credentials['server_id']        ?? $request->moduleSchedule->server_id,
                    'run_scheduled_at' => $credentials['run_scheduled_at'] ?? $request->moduleSchedule->run_scheduled_at,
                    'username_ssh'     => $credentials['username_ssh']     ?? $request->moduleSchedule->username_ssh,
                    'password_ssh'     => $credentials['password_ssh']     ?? $request->moduleSchedule->password_ssh,
                    'port_ssh'         => $credentials['port_ssh']         ?? $request->moduleSchedule->port_ssh,
                ]);

            DB::commit();
                return response()->json(['success' => true, 'msg' => 'edit module schedule successful', 'data' => $updateModuleSchedule]);

        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'msg' => 'module schedule not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
                throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $moduleSchedule = tap(ModuleSchedule::findOrFail($id))
                ->delete();

            DB::commit();
            return response()->json(['success' => true, 'msg' => 'delete module schedule successful', 'data' => $moduleSchedule]);

        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'msg' => 'module schedule not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
                throw $e;
        }
    }

    public function destroyByModuleId(int $module_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $schedules = ModuleSchedule::where('module_id', $module_id)->get();
            foreach ($schedules as $schedule) {
                $schedule->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'msg' => 'delete module schedule successful', 'data' => $schedules]);

        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'msg' => 'module schedule not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

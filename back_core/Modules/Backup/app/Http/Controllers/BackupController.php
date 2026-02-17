<?php

namespace Modules\Backup\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Backup\Http\Requests\ConfigBackup\EditBackupConfigRequest;
use Modules\Backup\Http\Requests\ConfigBackup\SetConfigBackupRequest;
use Modules\Backup\Http\Requests\TimeCronJob\SetTimeCronJobAndDestinationPathBackupRequest;
use Modules\Backup\Http\Requests\TimeCronJob\SetTimeCronJobBackupRequest;
use Modules\Backup\Models\BackupConfig;
use Modules\Backup\Models\BackupHistory;
use Modules\Backup\Services\BackupService;
use Modules\Backup\Services\CronTabService;
use Modules\Backup\Transformers\GetBackupConfigResource;
use Modules\User\Services\PaginationService;

class BackupController extends Controller
{
    public function __construct(private PaginationService $paginationService) {}

    public function index (Request $request): JsonResponse
    {
        $backupConfig = BackupConfig::with([
            'user:id,first_name,last_name,auth_name',
            'user.roles:name',
            'user.permissions:name'
        ])->get();

        return response()->json(['success' => true, 'data' => GetBackupConfigResource::collection($backupConfig)]);
    }
    public function create (SetConfigBackupRequest $request): JsonResponse
    {
        $credentials            = $request->validated();
        $credentials['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            $backupConfig = BackupConfig::create($credentials);

            $backupConfig->load([
                'user:id,first_name,last_name,auth_name',
                'user.roles:name',
                'user.permissions:name'
            ]);

//                    set cron job
            $cronTabService = new CronTabService($credentials['password']);
            $cronTabService->set();

            DB::commit();
            return response()->json(['success' => true, 'msg' => 'set config backup successFully', 'data' => new GetBackupConfigResource($backupConfig)], 200);

        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
                return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    public function edit (EditBackupConfigRequest $request): JsonResponse
    {
        $credentials            = $request->validated();
        $credentials['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

                $backupConfig = tap(BackupConfig::find($credentials['id']))
                    ->update($credentials);

                $backupConfig->load([
                    'user:id,first_name,last_name,auth_name',
                    'user.roles:name',
                    'user.permissions:name'
                ]);

            DB::commit();
                return response()->json(['success' => true, 'msg' => 'edit config backup successFully', 'data' => new GetBackupConfigResource($backupConfig)], 200);

        } catch (\Exception $e) {
            DB::rollback();
                throw $e;
        }
    }
    public function destroy (BackupConfig $backupConfig): JsonResponse
    {
        try {
            DB::beginTransaction();

                tap($backupConfig->delete());

            DB::commit();
                return response()->json(['success' => true, 'msg' => 'deleted backup config successFul', 'data' => $backupConfig], 200);

        } catch (\Exception $e) {
            DB::rollback();
                throw $e;
        }
    }



    public function getHistoryBackup (Request $request): JsonResponse
    {
        $perPage = ($request->input('paginate') ?? 10);

        return response()->json(['success' => true, 'data' => $this->paginationService->paginate(BackupHistory::query(), $request, ['id', 'name', 'status', 'created_at', 'updated_at'])]);
    }
}

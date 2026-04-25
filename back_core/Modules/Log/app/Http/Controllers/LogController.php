<?php

namespace Modules\Log\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Modules\User\Services\PaginationService;
use App\Http\Controllers\Contract\ApiController;
use Modules\Log\Http\Requests\Log\ShowAllLogsRequest;
use Modules\Log\Http\Requests\Log\ExportLogsRequest;

class LogController extends ApiController
{
    protected $paginationService;
    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function showAllLogs(ShowAllLogsRequest $request)
    {
        $logsQuery = $this->logsQuery($request);

        $logs = $this->paginationService->paginate($logsQuery, $request, ['id', 'log_name', 'description', 'created_at', 'updated_at']);

        return $this->respondSuccess('show log successFuly', $logs);
    }

    public function exportLogs(ExportLogsRequest $request)
    {
        $format = $request->input('format');
        $limit = min((int) $request->input('limit', 5000), 50000);
        $fileTimestamp = now()->format('Ymd_His');

        $logIds = $this->logsQuery($request)
            ->orderByDesc('id')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($logIds) {
                $output = fopen('php://output', 'w');
                fputcsv($output, ['id', 'log_name', 'description', 'created_at', 'updated_at']);

                Activity::query()
                    ->select(['id', 'log_name', 'description', 'created_at', 'updated_at'])
                    ->whereIn('id', $logIds)
                    ->orderByDesc('id')
                    ->chunkById(500, function ($logs) use ($output) {
                        foreach ($logs as $log) {
                            fputcsv($output, [
                                $log->id,
                                $log->log_name,
                                $log->description,
                                $log->created_at,
                                $log->updated_at,
                            ]);
                        }
                    }, 'id');

                fclose($output);
            }, "logs_{$fileTimestamp}.csv", [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        return response()->streamDownload(function () use ($logIds) {
            echo '[';
            $isFirst = true;

            Activity::query()
                ->select(['id', 'log_name', 'description', 'created_at', 'updated_at'])
                ->whereIn('id', $logIds)
                ->orderByDesc('id')
                ->chunkById(500, function ($logs) use (&$isFirst) {
                    foreach ($logs as $log) {
                        if (!$isFirst) {
                            echo ',';
                        }

                        echo json_encode([
                            'id' => $log->id,
                            'log_name' => $log->log_name,
                            'description' => $log->description,
                            'created_at' => $log->created_at,
                            'updated_at' => $log->updated_at,
                        ], JSON_UNESCAPED_UNICODE);

                        $isFirst = false;
                    }
                }, 'id');

            echo ']';
        }, "logs_{$fileTimestamp}.json", [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    private function logsQuery(Request $request)
    {
        return Activity::query()
            // search to name and description log
            ->when($request->input('search', ''), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('log_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            // show log-type
            ->when($request->input('type-log', ''), function ($query, $code) {
                return $query->where('properties', 'like', "%\"type-log\":\"{$code}\"%");
            })
            // if is set server_id to Auth user
            ->when(!empty(Auth::user()?->server_id), function ($query) {
                return $query->where('properties->server_id', Auth::user()->server_id);
            });
    }
}

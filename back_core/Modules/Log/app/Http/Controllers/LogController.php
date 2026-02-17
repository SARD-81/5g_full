<?php

namespace Modules\Log\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Modules\User\Services\PaginationService;
use App\Http\Controllers\Contract\ApiController;
use Modules\Log\Http\Requests\Log\ShowAllLogsRequest;

class LogController extends ApiController
{
    protected $paginationService;
    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function showAllLogs (ShowAllLogsRequest $request)
    {
        $logsQuery = Activity::query()
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

        $logs = $this->paginationService->paginate($logsQuery, $request, ['id', 'log_name', 'description', 'created_at', 'updated_at']);

        return $this->respondSuccess('show log successFuly', $logs);
    }
}

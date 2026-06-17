<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\Service\KpiService;

class KPIController extends Controller
{

    public function __construct(protected KpiService $service)
    {
    }

    public function index()
    {
        $items = $this->service->getAll();

        if (empty($items)) {
            return response()->json(['message' => 'No KPI data found.'], 404);
        }

        return response()->json($items);
    }

    public function initKPI(Request $request)
    {
        $request->validate([
            'file' => 'file|mimes:txt|max:10240',
            'path' => 'string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($request->path) {
                $this->service->setPath($request->path);
            }

            if ($request->hasFile('file')) {
                $this->service->initFromFile($request->file('file'));
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'msg' => $exception->getMessage(),
            ]);
        }
        $items = $this->service->getAll();
        return response()->json([
            'msg' => 'set kpi settings was successful.',
            'items' => $items
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:on,off',
//            'path' => 'required|string',
//            'server_ip' => 'required|string',
//            'username' => 'required|string',
//            'password' => 'required|string',
//            'remote_path' => 'required|string',
        ]);

        $items = $this->service->getAll();
        $item = collect($items)->firstWhere('id', $request->id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        if ($item['status'] === 'off' && $request->status === 'on') {

            $resp = $this->service->turnOn($item);

        } elseif ($item['status'] === 'on' && $request->status === 'off') {

            $resp = $this->service->turnOff($item);

        }

        $success = isset($resp) ? $resp['success'] : true;

        if ($success) {
            $this->service->updateStatus($request->id, $request->status);
        }

        return response()->json([
            'output' => $resp['output'] ?? null,
            'error' => $resp['error'] ?? null,
            'success' => $success,
            'msg' => $success ? 'turned ' . \request()->status : 'failed!'
        ], $success ? 200 : 400);
    }

}

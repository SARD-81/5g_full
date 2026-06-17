<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\SystemSetting\Http\Requests\DeleteMonitoringAddressReqest;
use Modules\SystemSetting\Http\Requests\SystemSetting\AddAddressRequest;
use Modules\SystemSetting\Http\Requests\SystemSetting\CreateMonitoringRequest;
use Modules\SystemSetting\Http\Requests\SystemSetting\EditMonitoringRequest;
use Modules\SystemSetting\Models\Monitoring;
use Modules\SystemSetting\Models\SystemSettings;
use PharIo\Manifest\Exception;

class MonitoringController extends Controller
{
    public function __construct()
    {}


    public function getAllMonitoringAddress()
    {
        return response()->json(['success' => true, 'data' => SystemSettings::select(['monitoring_attribute'])->get()]);
    }
    public function addMonitoringAddress (AddAddressRequest $request)
    {
        $credentials = $request->validated();

        try {
            $systemSetting = SystemSettings::first();
            if (!$systemSetting) {
                SystemSettings::create([
                    'monitoring_attribute' => [
                        'monitoring' => [$request['newMonitoring']]
                    ],
                ]);
            }


            if (! $request['inServiceNameExists'] && $systemSetting) {
//                  update monitoring filde
                $currentServices = $systemSetting->monitoring_attribute['monitoring'] ?? [];
                $updatedServices = array_merge($currentServices, [$request['newMonitoring']]);

                $systemSetting->update([
                    'monitoring_attribute' => [
                        'monitoring' => $updatedServices,
                    ],
                ]);
            }


            activity('add-monitpring-address')
                ->causedBy(Auth::user())
                ->event('create')
                ->withProperties([
                    'type-log' => 'system-setting',
                    'route' => request()->fullUrl(),
                    'method' => 'addMonitoringAddress',
                    'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                    'monitoring-data' => [
                        'name' => $credentials['name'],
                        'url' => $credentials['url'],
                    ]
                ])
                ->log('add monitoring address successful');


            return response()->json(['success' => true, 'message' => 'Add Address successful.'], 200);

        }catch (\Exception $e) {
            throw ValidationException::withMessages(['warning' => $e->getMessage()]);
        }
    }
    public function deleteMonitoringAddress (DeleteMonitoringAddressReqest $request)
    {
        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $systemSetting = SystemSettings::first();
            $monitoringServices = $systemSetting->monitoring_attribute['monitoring'] ?? [];

//            delete in monitorring service as list
            $filteredServices = array_filter($monitoringServices, function($service) use ($request) {
                return $service['name'] !== $request->name;
            });

            $systemSetting->update([
                'monitoring_attribute' => ['monitoring' => array_values($filteredServices)]
            ]);


            activity('add-monitpring-address')
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties([
                    'type-log' => 'system-setting',
                    'route' => request()->fullUrl(),
                    'method' => 'deleteMonitoringAddress',
                    'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                    'monitoring-data' => [
                        'name' => $credentials['name'],
                    ]
                ])
                ->log('deleted monitoring address successful');


            DB::commit();
                return response()->json(['success' => true, 'message' => 'Deleted SuccessFuly', 'data' => $filteredServices], 200);

        } catch (\Exception $e) {
            DB::rollBack();
                throw ValidationException::withMessages(['warning' => $e->getMessage()]);
        }
    }






    public function getAllMonitoring()
    {
        $monitoring = Monitoring::all();

        return response([
            'monitoring' => $monitoring,
        ]);
    }

    public function createMonitoring(CreateMonitoringRequest $request)
    {
        $monitoring = Monitoring::create([
            'title' => $request->title,
            'address' => $request->address,
        ]);

        return response([
            'monitoring' => $monitoring,
            'success' => true,
            'msg' => 'مورد جدید ساخته شد.',
        ]);
    }

    public function editMonitoring($monitoring_id, EditMonitoringRequest $request)
    {
        $monitoring = Monitoring::findOrFail($monitoring_id);

        $monitoring->update([
            'title' => $request->title ?? $monitoring->title,
            'address' => $request->address ?? $monitoring->address,
        ]);

        return response([
            'monitoring' => $monitoring,
            'success' => true,
            'msg' => 'ویرایش با موفقیت انجام شد.',
        ]);
    }

    public function deleteMonitoring($monitoring_id)
    {
        $monitoring = Monitoring::findOrFail($monitoring_id);

        $monitoring->delete();

        return response([
            'monitoring' => $monitoring,
            'success' => true,
            'msg' => 'مورد انتخاب شده با موفقیت حذف شد.',
        ]);
    }

}

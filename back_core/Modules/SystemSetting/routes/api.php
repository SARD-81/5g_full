<?php

use Illuminate\Support\Facades\Route;
use Modules\Server\Http\Controllers\CommandController;
use Modules\SystemSetting\Http\Controllers\CaptchaController;
use Modules\SystemSetting\Http\Controllers\KPIController;
use Modules\SystemSetting\Http\Controllers\SystemSettingsController;
use Modules\SystemSetting\Http\Controllers\TraceController;
use Modules\SystemSetting\Http\Controllers\RouteController;
use Modules\SystemSetting\Http\Controllers\MonitoringController;
use Modules\SystemSetting\Http\Controllers\RecapchaController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::get('get-status-reCapcha', [RecapchaController::class, 'getStatusReCapcha']);

Route::middleware(['auth:sanctum'])->group(function () {

//      Monitoring

    Route::middleware(['permission:monitoring|role:admin|expert'])->group(function () {

        Route::get('show-monitoring-address', [MonitoringController::class, 'getAllMonitoringAddress']);
        Route::post('add-monitoring-address', [MonitoringController::class,'addMonitoringAddress']);
        Route::delete('delete-monitoring-address', [MonitoringController::class,'deleteMonitoringAddress']);

    });

//     New Monitoring

    Route::middleware(['permission:monitoring|role:admin|expert'])->group(function () {

        Route::get('get-all-monitoring', [MonitoringController::class,'getAllMonitoring']);
        Route::post('create-monitoring', [MonitoringController::class,'createMonitoring']);
        Route::put('edit-monitoring/{monitoring_id}', [MonitoringController::class,'editMonitoring']);
        Route::delete('delete-monitoring/{monitoring_id}', [MonitoringController::class,'deleteMonitoring']);

    });

//      2FA
    Route::middleware(['role:admin'])->post('set-2FA', [SystemSettingsController::class, 'set2FA']);
    Route::withoutMiddleware(['auth:sanctum'])->get('get-2FA-status', [SystemSettingsController::class, 'getStatus2FA']);

//      Login SMS

    Route::middleware(['role:admin'])->group(function () {

        Route::post('set-login-sms-status', [SystemSettingsController::class, 'setLoginBySMS']);
        Route::post('set-config-connection-sms', [SystemSettingsController::class, 'setConfigConnectionSMS']);
        Route::get('get-config-connection-sms', [SystemSettingsController::class, 'getConfinConnectionSMS']);

    });

    Route::withoutMiddleware(['auth:sanctum'])->get('get-login-sms-status', [SystemSettingsController::class, 'getLoginBySMS']);
    Route::post('test-config-connection-sms', [SystemSettingsController::class, 'testConfigConnectionSMS']);
    Route::get('get-user-info-sms-panel', [SystemSettingsController::class, 'getUserInfoSMSPanel']);

//      reCaptcha
    Route::post('set-status-reCapcha', [RecapchaController::class, 'setStatusReCapcha']);
    Route::post('set-recapcha-data', [RecapchaController::class, 'setReatpchaData']);
    Route::get('get-recapcha-data', [RecapchaController::class, 'getRecaptchaData']);

    // motherboard
    Route::get('get-motherboard', [SystemSettingsController::class, 'getMotherboard']);

    Route::middleware(['role:admin'])->post('set-orginal-VM-ip',[SystemSettingsController::class, 'setOrginalVMIp']);
    Route::get('get-orginal-vm-ip', [SystemSettingsController::class, 'getOrginalVMIp']);


//      merge all routes
    Route::middleware(['role:admin|expert'])->get('get-all-config-system', [SystemSettingsController::class, 'getAllConfigSystem']);
    Route::middleware(['role:admin|expert'])->post('set-all-config-system', [SystemSettingsController::class, 'setAllConfigSystem']);

    Route::middleware(['role:admin|expert'])->get('get-subscriber-address', [SystemSettingsController::class, 'getSubscriberAddress']);
    Route::middleware(['role:admin|expert'])->post('set-subscriber-address', [SystemSettingsController::class, 'setSubscriberAddress']);


//    show vm interface
    Route::post('show-interface-vm', [CommandController::class, 'showInterfaceVm']);


//        trace server

    Route::middleware(['permission:trace|role:admin'])->group(function () {

        Route::post('trace-server-start', [TraceController::class, 'traceServerStart']);
        Route::post('trace-server-stop', [TraceController::class, 'traceServerStop']);

    });


//        route server

    Route::middleware(['permission:route|role:admin'])->group(function () {

        Route::post('show-route-server', [RouteController::class, 'showRouteServer']);
        Route::post('add-route-server', [RouteController::class, 'addRouteServer']);
        Route::post('delete-route-server', [RouteController::class, 'deleteRouteServer']);

    });


    Route::middleware(['permission:kpi|role:admin'])->prefix('kpi')->group(function () {

        Route::get('index', [KPIController::class, 'index']);
        Route::post('init', [KPIController::class, 'initKPI']);
        Route::put('update', [KPIController::class, 'update']);

    });

});

//      Captcha
Route::get('get-captcha', [CaptchaController::class, 'getCaptcha']);

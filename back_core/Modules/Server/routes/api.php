<?php

use Illuminate\Support\Facades\Route;
use Modules\Server\Http\Controllers\ModuleController;
use Modules\Server\Http\Controllers\ServerController;
use Modules\Server\Http\Controllers\CommandController;
use \Modules\Server\Http\Controllers\ModuleScheduleController;

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

Route::fallback(function () {
    return response()->json(['msg' => 'The address has been entered incorrectly.']);
});


Route::middleware(['auth:sanctum'])->group(function () {

    // server
    Route::middleware(['permission:VM/read|role:admin|visitor|expert'])->get('show-all-servers', [ServerController::class, 'showAllServers']);
    Route::middleware(['permission:VM/create|role:admin|expert'])->post('create-server', [ServerController::class, 'createServer']);
    Route::middleware(['permission:VM/update|role:admin|expert'])->put('edit-server', [ServerController::class, 'editServer']);
    Route::middleware(['permission:VM/delete|role:admin|expert'])->delete('server-delete', [ServerController::class, 'deleteServer']);
    Route::post('test-connection', [ServerController::class, 'testConnection']);

    // module
    Route::middleware(['permission:module/read|role:admin|visitor|expert'])->post('show-config-module/{serverID}/{ModuleID}', [ModuleController::class, 'showConfigModule']);
    Route::middleware(['role:admin|visitor|expert'])->post('show-all-servies-and-modules/{serverID}', [ModuleController::class, 'showAllServiseAndModulesInServer']);
    Route::middleware(['permission:module/create|role:admin|expert'])->post('create-module', [ModuleController::class, 'createModule']);
    Route::middleware(['permission:module/update|role:admin|expert'])->post('update-config-module', [ModuleController::class, 'updateConfigModule']);
    Route::middleware(['permission:module/delete|role:admin|expert'])->delete('delete-module', [ModuleController::class, 'deleteModule']);
    Route::middleware(['permission:module/read|role:admin|expert'])->get('show-all-modules', [ModuleController::class, 'showAllModules']);
    Route::middleware(['permission:module/update|role:admin|expert'])->post('edit-module', [ModuleController::class, 'editModule']);

    Route::resource('module/schedule', ModuleScheduleController::class)
        ->middleware(['role:admin']);

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('module/schedules-by-module/{module_id}', [ModuleScheduleController::class, 'destroyByModuleId']);
    });

//    Route::middleware(['role:admin'])->group(function () {
//        Route::get('module/schedule', [ModuleScheduleController::class, 'index'])->name('module.schedule.index');
//        Route::post('module/schedule', [ModuleScheduleController::class, 'store'])->name('module.schedule.store');
//        Route::put('module/schedule/{schedule}', [ModuleScheduleController::class, 'update'])->name('module.schedule.update');
//        Route::delete('module/schedule/{schedule}', [ModuleScheduleController::class, 'destroy'])->name('module.schedule.destroy');
//    });


//      command service
    Route::middleware(['permission:ping|role:admin|expert'])->post('ping-ssh', [CommandController::class, 'PingServer']);

    Route::middleware(['permission:module/update|role:admin|expert'])->group(function () {

        Route::post('restart-service-config', [CommandController::class, 'restartServiceModule']);
        Route::post('start-service-config', [CommandController::class, 'startServiceModule']);
        Route::post('stop-service-config', [CommandController::class, 'stopServiceModule']);
        Route::post('status-service-config', [CommandController::class, 'statusServiceModule']);

        Route::post('undo-module-config', [ModuleController::class, 'undoConfigModule']);
        Route::post('undo-to-initial-config-modules', [ModuleController::class, 'undoToInitialConfigModule']);

    });

    Route::middleware(['permission:module/read|role:admin|expert'])->post('export-module-file', [ModuleController::class, 'expertModuleFileIsServer']);

    // power on | off server
    Route::middleware(['permission:VM/status|role:admin|expert'])->post('server-stop', [ServerController::class, 'serverStop']);
    Route::middleware(['permission:VM/status|role:admin|expert'])->post('server-start', [ServerController::class, 'serverStart']);
    Route::middleware(['role:admin|expert|visitor'])->post('server-status', [ServerController::class, 'serverStatus']);
});
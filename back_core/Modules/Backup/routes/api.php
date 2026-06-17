<?php

use Illuminate\Support\Facades\Route;
use Modules\Backup\Http\Controllers\BackupController;

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

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('get-config-backup', [BackupController::class, 'index']);
    Route::post('set-config-backup', [BackupController::class, 'create']);
    Route::patch('edit-config-backup', [BackupController::class, 'edit']);
    Route::delete('delete-config-backup/{backupConfig}', [BackupController::class, 'destroy']);

    Route::get('get-history-backup', [BackupController::class, 'getHistoryBackup']);
});

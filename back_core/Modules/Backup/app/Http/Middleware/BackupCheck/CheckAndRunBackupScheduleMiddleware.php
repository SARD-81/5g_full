<?php

namespace Modules\Backup\Http\Middleware\BackupCheck;

use Closure;
use Illuminate\Http\Request;

class CheckAndRunBackupScheduleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {

    }
}

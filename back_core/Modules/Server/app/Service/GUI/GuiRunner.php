<?php

namespace Modules\Server\Service\GUI;

class GuiRunner
{
    public function execute()
    {
        try {

            // مسیر اجرای برنامه (مثال: Wine روی لینوکس)
            $winePath = '/usr/bin/wine';
            $exePath  = '';

            if (!file_exists($exePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'فایل برنامه یافت نشد'
                ], 404);
            }

            // اجرای non-blocking
            exec("$winePath '$exePath' > /dev/null 2>&1 &");

            return response()->json([
                'status' => 'success',
                'message' => 'برنامه با موفقیت اجرا شد'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\User\Models\User;
use Illuminate\Support\Facades\Route;

//Route::get('test-c', function (Request $request) {
//
//    $host = '8.8.8.8';
//    $port = 443;
//    $timeout = 5;
//
//    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
//
//    if ($fp) {
//        // بستن اتصال
//        fclose($fp);
//        return "✅ اتصال به پورت {$port} آدرس {$host} برقرار است.";
//    } else {
//        // در صورت عدم موفقیت، شماره خطا و پیام را برمی‌گرداند
//        return "❌ اتصال به {$host}: خطا $errno - $errstr";
//    }
//
//
//
//
//    $host = '8.8.8.8';
//    $count = 1; // تعداد پینگ‌ها
//
//    // دستور ping مخصوص لینوکس (برای ویندوز باید از '-n' به جای '-c' استفاده کرد)
//    $command = "ping -c {$count} {$host}";
//
//    // اجرای دستور
//    $output = shell_exec($command);
//
//    // بررسی خروجی
//    if (str_contains($output, 'received, 0% packet loss') || str_contains($output, 'bytes from')) {
//        return "✅ اتصال به {$host} موفقیت‌آمیز است.";
//    } else {
//        return "❌ اتصال به {$host} برقرار نشد.";
//    }
//
//
//    $ip = '8.8.8.8';
//    exec("ping -c 1 $ip", $output, $result);
//    return response([
//        'result' => $result,
//        'output' => $output,
//    ]);
//    $validator = Validator::make($request->all(), [
//        'captcha' => 'required|captcha_api:' . $request->input('captcha_key') . ',default',
//    ]);
//
//    if ($validator->fails()) {
//        return response()->json([
//            'status' => false,
//            'errors' => $validator->errors()
//        ], 422);
//    }
//
//    // اگر کپچا درست بود:
//    return response()->json(['status' => true, 'message' => 'Captcha passed!']);
//});

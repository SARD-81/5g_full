<?php

namespace Modules\SystemSetting\Service;

use Illuminate\Validation\ValidationException;

class ConnectionTestPublicDomainService
{
    public static function testConnection (string $ip)
    {
        try {

//            $port = 53;
            $port = 443;
            $timeout = 5; // تایم اوت بر حسب ثانیه

            // @ جلوی تابع برای جلوگیری از نمایش خطاهای PHP در صورت عدم اتصال
            $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);

            if ($fp) {
                // بستن اتصال
                fclose($fp);
                return true;
                return "✅ اتصال به پورت {$port} آدرس {$host} برقرار است.";
            } else {
                return false;
                // در صورت عدم موفقیت، شماره خطا و پیام را برمی‌گرداند
                return "❌ اتصال به {$host}: خطا $errno - $errstr";
            }

//            exec("ping -c 1 -W 1 $ip", $output, $result);
//
//            if ($result === 0)
//                return true;
//
//            return false;

        } catch (\Exception $e) {
            throw ValidationException::withMessages(['server_conenction' => 'The connection to the server failed:' . $e->getMessage()]);
        }
    }
}

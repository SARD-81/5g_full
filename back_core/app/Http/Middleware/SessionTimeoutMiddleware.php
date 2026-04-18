<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // اگر کاربر لاگین نکرده است، نیازی به چک کردن نیست
        if (!Auth::check()) {
            return $next($request);
        }

        $timeout = 30 * 60; // 30 دقیقه به ثانیه
        $lastActivity = session('last_activity_time');

        // بررسی اینکه آیا از آخرین فعالیت کاربر بیش از 30 دقیقه گذشته است یا خیر
        if ($lastActivity && (time() - $lastActivity > $timeout)) {
            // خروج کاربر
            Auth::logout();
            
            // از بین بردن سشن فعلی برای جلوگیری از حملات Session Fixation
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // هدایت به صفحه لاگین با پیام خطا
            return redirect()->route('login')->withErrors([
                'timeout' => 'به دلیل ۳۰ دقیقه عدم فعالیت، جهت حفظ امنیت از سیستم خارج شدید. لطفاً مجدداً وارد شوید.'
            ]);
        }

        // بروزرسانی زمان آخرین فعالیت برای درخواست فعلی
        session(['last_activity_time' => time()]);

        return $next($request);
    }
}

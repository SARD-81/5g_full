<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\Http\Requests\Capcha\SetReCaptchaDataRequest;
use Modules\SystemSetting\Http\Requests\Capcha\SetStatusReCapchaRequest;
use Modules\SystemSetting\Models\SystemSettings;

class RecapchaController extends Controller
{
    public function __construct()
    {}


//    online capcha
    public function setStatusReCapcha (SetStatusReCapchaRequest $request)
    {
        $creadtioanle = $request->validated();

        try {
            DB::beginTransaction();

            $systemSetting = SystemSettings::first();

            !$systemSetting
                ? $systemSetting = $systemSetting->create($creadtioanle)
                : $systemSetting->update(['active_online_capcha'
            => $creadtioanle['active_online_capcha'] ?? $systemSetting['active_online_capcha']]);


            activity('')
                ->causedBy(Auth::user())
                ->event('update')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'setLoginBySMS',
                    'user' =>  Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' =>Auth::user()->roles()->pluck('name')->first(),
                    'recapcha' => $systemSetting->active_online_capcha
                ])
                ->log('change of status recapcha the system');


            DB::commit();
            return response()->json(['success' => true, 'msg' => 'Settings have been successfully applied.',
                'data' => $systemSetting->active_online_capcha], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'An issue occurred in the application process.'], 500);
        }
    }
    public function getStatusReCapcha ()
    {
        return response()->json(['success' => true, 'data' => SystemSettings::first()->active_online_capcha], 200);
    }


//    set data captcha
    public function setRecatpchaData (SetReCaptchaDataRequest $request)
    {
        $creadtioanle = $request->validated();

        try {
            DB::beginTransaction();

            $systemSetting = SystemSettings::first();

            $systemSetting
                ? $systemSetting->update([
                'recaptcha_secret_key' => Crypt::encrypt($creadtioanle['recaptcha_secret_key'] ?? $systemSetting['recaptcha_secret_key']),
                'recaptcha_site_name' => Crypt::encrypt($creadtioanle['recaptcha_site_name'] ?? $systemSetting['recaptcha_site_name'])
            ])
                : $systemSetting = $systemSetting->create([
                'recaptcha_secret_key' => Crypt::encrypt($creadtioanle['recaptcha_secret_key'] ?? $systemSetting['recaptcha_secret_key']),
                'recaptcha_site_name' => Crypt::encrypt($creadtioanle['recaptcha_site_name'] ?? $systemSetting['recaptcha_site_name'])
            ]);


            DB::commit();
            return response()->json([
                'success' => true,
                'msg' => 'Settings have been successfully applied.',
                'data' => [
                    'recaptcha_secret_key' => Crypt::decrypt(SystemSettings::first()['recaptcha_secret_key']),
                    'recaptcha_site_name' => Crypt::decrypt(SystemSettings::first()['recaptcha_site_name'])
                ]], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'An issue occurred in the application process.'], 500);
        }
    }
    public function getRecaptchaData()
    {
        return SystemSettings::first()->config_connection_sms ?? null
            ? response()->json([
                'success' => true,
                'data' => [
                    'recaptcha_secret_key' => Crypt::decrypt(SystemSettings::first()->recaptcha_secret_key),
                    'recaptcha_site_name' => Crypt::decrypt(SystemSettings::first()->recaptcha_site_name)
                ]
            ], 200)
            : response()->json([
                'success' => true,
                'msg' => 'no content'
            ]);
    }

}

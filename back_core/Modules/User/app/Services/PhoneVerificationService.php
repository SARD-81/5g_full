<?php

namespace Modules\User\Services;



use App\Http\Controllers\Contract\ApiController;
use http\Exception\RuntimeException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Modules\SystemSetting\Models\SystemSettings;
use Modules\SystemSetting\Service\SmsStatusService;
use Modules\User\Models\PhoneLogin;
use Modules\User\Models\PhoneResetPassword;
use Modules\User\Models\PhoneVerificationToken;
use Modules\User\Models\User;


class PhoneVerificationService extends ApiController
{
    /**
     * verify user phone
     */
    public function checkPhoneDetail(object $user, string $status)
    {
        if (!$user->phone)
            throw new HttpResponseException(response(['msg' => 'You have not registered your phone!'], 422));


        if ($user->phone_verified_at)
            throw new HttpResponseException(response(['msg' => 'Your number has already been verified!'], 422));


        switch ($status) {
            case "sendVerify":
                return $this->handleSendVerify($user['phone']);

            case "verify":
                return $this->handleVerify($user['phone']);
        }
    }
    private function handleSendVerify(string $phone) : void
    {
        $sentPhone = PhoneVerificationToken::firstWhere('phone', $phone);

        if ($sentPhone) {
            if (!$sentPhone->expired_at >= now())
                    $sentPhone->delete();

        }
    }
    private function handleVerify(string $phone)
    {
        $sendPhone = PhoneVerificationToken::firstWhere('phone', $phone);

        if ($sendPhone) {
            if ($sendPhone['expired_at'] <= now()) {
                $sendPhone->delete();

                throw new HttpResponseException(response(['msg' => 'The sent code has expired!'], 422));
            }

            return;
        }

        throw new HttpResponseException(response(['msg' => 'The verification code has either not been sent or is invalid!'], 422));
    }





    /**
     * Send verification code to a user
     */
    public function sendVerificationCode(
        string $template,
        string $code,
        string $phone,
        string $message,
        User $user = null
    ): void {
       try {

            $this->storeVerificationCode($template, $code, $phone);


                    // SMS panle SunwaysmsService SOAP SERVICE
             $connectionData = SystemSettings::first()->config_connection_sms ?? null
                 ? Crypt::decrypt(SystemSettings::first()->config_connection_sms)
                 : throw ValidationException::withMessages(['validation' => ['no connection config data to panel SMS']]);

                 if ($connectionData) {

                         // HTTP SERVICE
                     $smsService = new SMSService();
                     $smsService->sendMessageAsync(
                         $connectionData['username'] ?? null,
                         $connectionData['password'] ?? null,
                         [$phone],
                         $message,
                         $connectionData['special_number'] ?? null,
                         false,
                         [$code]
                     );

                 } else
                     throw ValidationException::withMessages(['validation' => ['no connection config data to panel SMS']]);

       } catch (\Exception $e) {
            throw $e;
       }
    }
    private function storeVerificationCode(string $template, string $code, string$phone)
    {
        try {
            switch ($template) {
                case "Verify":
                    PhoneVerificationToken::create([
                        "phone" => $phone,
                        "token" => $code,
                        "expired_at" => now()->addMinutes(2),
                    ]);
                    break;
                case "ResetPassword":
                    PhoneResetPassword::create([
                        "phone" => $phone,
                        "token" => $code,
                        "expired_at" => now()->addMinutes(2),
                    ]);
                    break;
                case "PhoneLogin":
                    PhoneLogin::create([
                        "phone" => $phone,
                        "token" => $code,
                        "expired_at" => now()->addMinutes(2),
                    ]);
                    break;
                case 'testConnection':
                    true;
                    break;
                default :
                    throw new RuntimeException('invalide template sms panel.');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }



    /**
     * get user info panel sms sanway
     */
    public function getUserAccountInfo ()
    {
        try {

            // SMS panle SunwaysmsService SOAP SERVICE
            $connectionData = SystemSettings::first()->config_connection_sms ?? null
                ? Crypt::decrypt(SystemSettings::first()->config_connection_sms)
                : throw ValidationException::withMessages(['validation' => ['no connection config data to panel SMS']]);

            if ($connectionData) {

                // HTTP SERVICE
                $smsService = new SMSService();
                $output = $smsService->getUserInfo(
                    $connectionData['username'] ?? null,
                    $connectionData['password'] ?? null,
                );

            } else
                throw ValidationException::withMessages(['validation' => ['no connection config data to panel SMS']]);


            return $output;

        } catch (\Exception $e) {
            throw $e;
        }
    }



/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////                               //////////////////////////////////////
////////////////////////////////    Reset Password By Phone    //////////////////////////////////////
////////////////////////////////                               //////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

    public function checkPhoneIsVerified($phone)
    {
        $user = User::firstWhere('phone', $phone);
        if (!$user->phone_verified_at)
            throw new HttpResponseException(response(['msg' => 'شماره تلفن مورد نظر تأیید نشده است!'], 422));

    }
    public function checkCodeValidation($phone)
    {
        $sent_phone = PhoneResetPassword::firstWhere('phone', $phone);

        if (!$sent_phone)
            throw new HttpResponseException(response(['msg' => 'کد تایید یا ارسال نشده است یا معتبر نیست!'], 422));


        // اگر کد ارسال شده منقضی شده باشد
        if ($sent_phone->expired_at < now()->toDateTimeString()) {
            $sent_phone->delete();

            throw new HttpResponseException(response(['msg' => 'کد ارسالی منقضی شده است!'], 422));
        }

        return response(['msg' => 'کد ارسال شده معتبر است.'], 200);

    }
    public function isSmsSent($phone)
    {
        $sent_phone = PhoneResetPassword::firstWhere('phone', $phone);

        if ($sent_phone){
            if ($sent_phone->expired_at >= now()->toDateTimeString())
                    $sent_phone->delete();
        }
    }
    public function changePassword(string $phone, string $code, string $password)
    {
        $correct_code = PhoneResetPassword::firstWhere('phone', $phone);

        if (!$correct_code || $correct_code->token !== $code) {
            activity('entered-code-is-not-correct')
                ->causedBy(Auth::user())
                ->performedOn($correct_code)
                ->withProperties([
                    'code' => '5',
                    'route' => request()->fullUrl(),
                    'method' => 'changePassword',
                ])
                ->log('کد وارد شده صحیح نیست');

            return response(['msg' => 'کد وارد شده صحیح نیست!'], 422);
        }

        // پیدا کردن کاربر و به روزرسانی رمز عبور
        $user = User::firstWhere('phone', $phone);
        try {
            $user->update(['password' => $password]);
            $correct_code->delete();

            activity('login-in-phone')
                ->causedBy(Auth::user())
                ->performedOn($correct_code)
                ->withProperties([
                    'route' => request()->fullUrl(),
                    'method' => 'changePassword',
                ])
                ->log('کاربر توسط شماره تماس رمز عبور خود را تغییر داد');

            return response(['msg' => 'کلمه عبور شما تغییر یافت'], 200);
        } catch (\Exception $e) {
            activity('not-verification-phone')
                ->causedBy(Auth::user())
                ->performedOn($correct_code)
                ->event('100')
                ->withProperties([
                    'route' => request()->fullUrl(),
                    'method' => 'changePassword',
                    'Errors' => $e->getMessage()
                ])
                ->log('هنگام عوض کردن رمز کاربر توسط شماره تماس این خطا رخ داد');

            throw new HttpResponseException(response(['msg' => 'مشکلی در پایگاه داده به وجود آمد!', 'error' => $e->getMessage(), 'code' => '100'], 400));
        }

    }



/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////                               //////////////////////////////////////
////////////////////////////////        Login By Phone         //////////////////////////////////////
////////////////////////////////                               //////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

    public function checkLoginCode($phone)
    {
        $sent_phone = PhoneLogin::firstWhere('phone', $phone);

        if (!$sent_phone)
            return response(['msg' => 'The verification code has either not been sent or is invalid!'], 422);


        // بررسی انقضای کد
        if ($sent_phone->expired_at < now()) {
            $sent_phone->delete();

            return response(['msg' => 'The sent code has expired!'], 422);
        }
    }
    public function isLoginSent($phone){
        $sent_phone = PhoneLogin::firstWhere('phone', $phone);

        if ($sent_phone)
            $sent_phone->delete();
    }

    public function login(string $phone, string $code)
    {
        $correct_code = PhoneLogin::firstWhere('phone', $phone);

//              validation code
        if (!$correct_code || $correct_code->token !== $code)
            return response(['msg' => 'The entered code is incorrect!'], 422);


        $user = User::firstWhere('phone', $phone);
        if (!$user)
            return response(['msg' => 'No user was found with this phone number!'], 404);


        try {

            $token = $user->createToken('apiToken')->plainTextToken;
            $correct_code->delete();

            return $this->respondSuccess('The user has logged in', [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'auth_name' => $user->auth_name,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response(['msg' => 'مشکلی در پایگاه داده به وجود آمد!', 'error' => $e->getMessage(), 'code' => '100'], 400);
        }

    }

}


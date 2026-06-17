<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Modules\SystemSetting\Models\SystemSettings;
use Modules\User\Http\Requests\Auth\Login2FARequest;
use Modules\User\Http\Requests\Auth\Loginrequest;
use Modules\User\Http\Requests\Phone\LoginPhoneRequest;
use Modules\User\Http\Requests\Phone\SendLoginPhoneRequest;
use Modules\User\Http\Requests\ReCaptcha\ValidateReCaptchaTokenRequest;
use Modules\User\Models\PhoneLogin;
use Modules\User\Models\User;
use Modules\User\Services\PhoneVerificationService;

class AuthController extends ApiController
{
    public function __construct(private PhoneVerificationService $phoneService)
    {}


    public function login (Loginrequest $request)
    {
        if (!SystemSettings::first()->active_online_capcha) {
            $validator = Validator::make($request->all(), [
                'captcha' => 'required|captcha_api:' . $request->input('captcha_key') . ',default',
            ], [
                'captcha' => 'captcha is invalid!',
                'captcha_key' => 'captcha is invalid!',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        $credentials = $request->validated();

        $user = User::whereRaw('BINARY auth_name = ?', [$credentials['auth_name']])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password))
            return response()->json(['msg' => 'You have entered an incorrect username or password'], 422);

        $user->tokens()->delete();
        $token = $user->createToken('apiToken')->plainTextToken;


            activity('login')
            ->event('login')
                ->causedBy(Auth::user())
                ->withProperties([
                    'route' => request()->fullUrl(),
                    'method' => 'login',
                    'user' => $user,
                ])
                ->log('The user logged in with the username and password.');
            $is2FAEnabled = SystemSettings::first()?->is_login_2FA;

            if (!$is2FAEnabled) {
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
            } else {
                $this->phoneService->isLoginSent($user['phone']);


                $template = "PhoneLogin";
                $param1 = rand(100000, 999999); // random code

                $this->phoneService->sendVerificationCode($template, $param1, $user['phone'], $user);

                return response()->json(['success' => true, 'msg' => 'The SMS has been sent successfully.'], 200);
            }
    }
    public function logout(Request $request)
    {
        try {

            $user = $request->user();
            $user->tokens()->delete();

            activity('logout')
                ->causedBy(Auth::user())
                ->event('logout')
                ->withProperties([
                    'type-log' => 'app',
                    'route' => request()->fullUrl(),
                    'method' => 'logout',
                    'user' => $user
                ])
            ->log('The user has logged out of their account');

            return $this->respondSuccess('The user has logged out of their account', ['user' => $user]);

        } catch (\Exception $e) {
            return response()->json(['msg' => 'An error occurred while logging out the user']);
        }
    }
    public function login2FA (Login2FARequest $request)
    {
        $credentials = $request->validated();

        $user = User::find($credentials['user_id']);

        $this->phoneService->checkLoginCode($user['phone']);

        $correct_code = PhoneLogin::firstWhere('phone', $user->phone);

//        validation code
        if (!$correct_code || $correct_code->token !== $credentials['code'])
            return response(['msg' => 'The entered code is incorrect!'], 422);


        $user = User::firstWhere('phone', $user->phone);
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
            return response(['success' => true, 'There was a problem with the program process.'], 400);
        }
    }





        // Phone
    public function sendLoginPhone(SendLoginPhoneRequest $request)
    {
        if (!SystemSettings::first()->active_online_capcha) {
            $validator = Validator::make($request->all(), [
                'captcha' => 'required|captcha_api:' . $request->input('captcha_key') . ',default',
            ], [
                'captcha' => 'captcha is invalid!',
                'captcha_key' => 'captcha is invalid!',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        $credentials = $request->validated();

        $this->phoneService->isLoginSent($credentials['phone']);

        $template = "PhoneLogin";
        $code = rand(100000, 999999); // random code

        $message = "code login to 5G Application: $code";

        $this->phoneService->sendVerificationCode($template, $code, $credentials['phone'], $message);

        return response()->json(['success' => true, 'msg' => 'The SMS has been sent successfully.'], 200);
    }
    public function loginPhone(LoginPhoneRequest $request)
    {
        $credentials = $request->validated();

        $this->phoneService->checkLoginCode($credentials['phone']);

        return $this->phoneService->login($request->phone, $request->code);

    }



    public function validateReCaptchaToken (ValidateReCaptchaTokenRequest $request)
    {
        $credentials = $request->validated();

        $response = Http::asForm()->post(env('RECAPTCHA_VERYFY'), [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $credentials['response'],
            'remoteip' => request()->ip(),
        ]);

        return $response->json()['success'] == true ?? null
            ? Response::json(['success' => true, 'data' => $response->json()], 200)
            : Response::json(['success' => false, 'data' => $response->json()], 422);
    }
}

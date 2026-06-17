<?php

namespace Modules\SystemSetting\Http\Requests\FA2;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\Models\SystemSettings;
use Modules\User\Models\User;
use Modules\User\Services\PhoneVerificationService;

class Set2FAReqest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'is_login_2FA' => ['required', 'boolean'],
        ];
    }



    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;



        $systemSetting = SystemSettings::first();
        $validator->after(function ($validator) use ($systemSetting) {

            if ($this->input('is_login_2FA') === true) {
                if (!$systemSetting['config_connection_sms'])
                    return $validator->errors()->add('config_connection_sms', 'You have not entered your SMS panel information.');
            }


//        send messnage to admin phone
            $userAdmin = User::Role('admin')->first();

            $phoneVarificationService = new PhoneVerificationService();

            $status2FA = $this->input('is_login_2FA') ? 'on.' : 'off.';

            $message = '5G Application : Change status 2FA to ' . $status2FA;

            $phoneVarificationService->sendVerificationCode(
                'testConnection',
                rand(100000, 999999),
                $userAdmin['phone'],
                $message
            );



            $this->merge([
                'systemSetting' => $systemSetting
            ]);
        });
    }



    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

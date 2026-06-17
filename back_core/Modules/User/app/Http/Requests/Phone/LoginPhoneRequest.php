<?php

namespace Modules\User\Http\Requests\Phone;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\Models\SystemSettings;

class LoginPhoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^09\d{9}$/', 'exists:users,phone'],
            'code' => ['required', 'string', 'max:6'],
        ];
    }


    public function withValidator ($validator)
    {
        $is2FA = SystemSettings::first()->pluck('is_login_2FA');

        $validator->after(function ($validator) use ($is2FA){

            if ($is2FA && !$this->input('phone'))
                return $validator->errors()->add('validation', '2FA is enabled; a phone number is required.');

            $systemSetting = SystemSettings::first();
                if (!$systemSetting['is_login_sms'])
                    return $validator->errors()->add('validation', 'login by phone stopped');
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

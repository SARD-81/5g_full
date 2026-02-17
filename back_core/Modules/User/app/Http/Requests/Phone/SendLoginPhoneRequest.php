<?php

namespace Modules\User\Http\Requests\Phone;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\Models\SystemSettings;

class SendLoginPhoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^09\d{9}$/', 'exists:users,phone'],
        ];
    }




    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;

        $validator->after(function ($validator) {

            $systemSetting = SystemSettings::first();
                if (!$systemSetting['is_login_sms'])
                    $validator->errors()->add('validation', 'login by phone stopped');
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

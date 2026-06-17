<?php

namespace Modules\SystemSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\Models\SystemSettings;

class TestConfigConnectionSMSRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
        ];
    }


    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;


            $systemSetting = SystemSettings::first();
        $validator->after(function ($validator) use ($systemSetting) {

            if (! $systemSetting['config_connection_sms'])
                return $validator->errors()->add('config_connection_sms', 'You have not entered your SMS panel information.');

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

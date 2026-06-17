<?php

namespace Modules\SystemSetting\Http\Requests\Merged;

use Illuminate\Foundation\Http\FormRequest;

class MergeAllConfigSystemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'zabbix_address' => ['nullable', 'string', 'min:1', 'max:512'],
            'elk_address' => ['nullable', 'string','min:1','max:512'],
            'is_login_2FA' => ['nullable', 'boolean'],
            'is_login_sms' => ['nullable', 'boolean'],
            'orginal_vm_ip' => ['nullable', 'string', 'ip'],
//            'subscriber_address' => ['nullable', 'string', 'max:255'],
        ];
    }



    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;


        $validator->after(function ($validator) {

            if (!$this->input('is_login_sms') && $this->input('is_login_2FA'))
                return $validator->errors()->add('validation', 'Wrong data to false is_login_sms and true is_login_2FA');
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

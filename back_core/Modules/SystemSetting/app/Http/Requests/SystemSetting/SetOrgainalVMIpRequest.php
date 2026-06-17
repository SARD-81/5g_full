<?php

namespace Modules\SystemSetting\Http\Requests\SystemSetting;

use Illuminate\Foundation\Http\FormRequest;

class SetOrgainalVMIpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'orginal_vm_ip' => ['required', 'string', 'ip']
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

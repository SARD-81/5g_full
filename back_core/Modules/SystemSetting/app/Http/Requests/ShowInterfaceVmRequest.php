<?php

namespace Modules\SystemSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowInterfaceVmRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'servers' => ['required', 'array'],
            'servers.*.id'       => ['required', 'integer', 'exists:servers,id'],
            'servers.*.password' => ['required', 'string'],
            'servers.*.username' => ['required', 'string'],
            'servers.*.port'     => ['numeric']
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

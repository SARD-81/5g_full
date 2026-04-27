<?php

namespace Modules\Server\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class ShowConfigModuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'server_id' => ['required', 'integer', 'exists:servers,id'],
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'port' => ['nullable', 'integer', 'between:1,65535'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
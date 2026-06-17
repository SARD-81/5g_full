<?php

namespace Modules\Server\Http\Requests\SshServer;

use Illuminate\Foundation\Http\FormRequest;

class SshServerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'server_id' => ['required', 'integer', 'exists:servers,id'],
            'ipـdestination' => ['required', 'regex:/^(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))$|^(([0-9a-fA-F]{1,4}:){7}([0-9a-fA-F]{1,4}|:))$/'],
            'Interface' => ['string'],

            'username' => ['required', 'string', 'min:1', 'max:128'],
            'password' => ['required', 'string', 'min:1', 'max:128'],
            'port' => ['nullable', 'integer', 'min:1'],
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

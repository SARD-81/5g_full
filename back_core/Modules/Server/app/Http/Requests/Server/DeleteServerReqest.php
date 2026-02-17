<?php

namespace Modules\Server\Http\Requests\Server;

use Illuminate\Foundation\Http\FormRequest;

class DeleteServerReqest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'server_id' => ['required', 'integer', 'exists:servers,id'],
            'auth_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255']
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

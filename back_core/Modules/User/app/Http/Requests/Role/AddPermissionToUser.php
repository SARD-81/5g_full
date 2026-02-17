<?php

namespace Modules\User\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class AddPermissionToUser extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'permission_name' => ['required', 'string', 'exists:permissions,name']
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

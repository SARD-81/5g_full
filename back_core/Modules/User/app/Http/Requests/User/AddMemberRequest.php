<?php

namespace Modules\User\Http\Requests\User;

use Illuminate\Validation\Rule;
use Modules\Server\Models\Server;
use Modules\User\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddMemberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:191', 'min:3'],
            'last_name' => ['nullable', 'string', 'max:191', 'min:3'],
            'auth_name' => ['required', 'string', Rule::unique('users', 'auth_name')->whereNull('deleted_at'), 'min:3', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^09\d{9}$/', Rule::unique('users', 'phone')->whereNull('deleted_at'),],
            'role' => ['required', 'in:visitor,expert'],
            'permission_name' => ['nullable', 'array'],
            'permission_name.*' => ['required', 'string', 'exists:permissions,name'],
            'password' => ['required', Password::min(8), 'confirmed', 'max:60'],

            'server_id' => ['integer', 'exists:servers,id']
        ];
    }





    public function ValidationServerPermission ($server, $permissionNames)
    {
                // assessing permission to motherboard server
        $serverPermissionsRequest = array_filter(
            $permissionNames,
            fn($permission) => str_starts_with($permission, 'server/')
        );

        if ($serverPermissionsRequest)
            throw ValidationException::withMessages(['validation' => ['not set server permission to create user to motherboard']]);



            // chacke send permission unauthorized
        $arrayUnauthorizedPermission = ['VM/create', 'VM/update', 'VM/delete', 'VM/status'];

            if(!empty(array_intersect($arrayUnauthorizedPermission, $permissionNames)))
                throw ValidationException::withMessages(['validation' => ['permission Unauthorized to give User motherboard. You cannot grant server administrative access to this user.']]);



        $this->merge(['serverPermission' => 'server/' . $server['name']]);

    }
    public function validationUserPermission ($permissionNames)
    {
        $serverPermissions = Permission::where('name', 'like', 'server/%')->pluck('name')->toArray();
        if (! $serverPermissions)
            throw ValidationException::withMessages(['validation' => ['server permission empity']]);

        if (empty($permissionNames) || empty(array_intersect($permissionNames, $serverPermissions))) {
            throw ValidationException::withMessages(['validation' => ['At least one server-related permission is required']]);
        }

    }
    public function withValidator ($validator)
    {
        if ($validator->errors()->any())
            return;


            $server = Server::find($this->input('server_id'));
        $validator->after(function ($validator) use($server) {

            $permissionNames = $this->input('permission_name') ?? null;


            $this->input('server_id')
                ? $this->validationServerPermission($server, $permissionNames)
                : $this->validationUserPermission($permissionNames);


            $this->merge(['permissionNames' => $permissionNames]);

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

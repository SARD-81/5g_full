<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use App\Http\Controllers\Contract\ApiController;
use Modules\User\Http\Requests\Role\AddPermissionToUser;
use Modules\User\Models\Permission;
use Modules\User\Transformers\Role\ShowAllRoleResource;

class RoleController extends ApiController
{
    public function showAllRolesPermissions (Request $request)
    {
            // old code And read defalte permission assign to role
        // $roles = Role::with('permissions:name')->get();


            // now code (hard code in clear defalte permssion this role)
        $serverPermissions = Permission::where('name', 'like', 'server/%')->pluck('name')->toArray();

        $roles = [
            [
                'name' => 'admin',
                'permissions' => Permission::where('name', 'not like', 'server%')
                        ->pluck('name')
                        ->toArray(),
            ],
            [
                'name' => 'visitor',
                'permissions' => array_merge(
                    ['VM/read', 'module/read', 'monitoring'],
                   $serverPermissions
                ),
            ],
            [
                'name' => 'expert',
                'permissions' => array_merge(
                    [
                        'VM/read', 'VM/create', 'VM/update', 'VM/delete',
                        'module/read', 'module/create', 'module/update', 'module/delete',
                        'monitoring'
                    ],
                   $serverPermissions
                ),
            ]
        ];

        return $this->respondSuccess('List of all roles with access', $roles);
    }
    public function showAllPermissions ()
    {
        return $this->respondSuccess('List of all permissions', Permission::cursor()->pluck('name'));
    }


    public function addPermissionToUser(AddPermissionToUser $request)
    {
        $credentials = $request->validated();

        $user = User::find($credentials['user_id']);
        $permissionName = $request->input('permission_name');

        $user->givePermissionTo($permissionName);

        return response()->json(['message' => 'Permission was successfully added to the user']);
    }
    public function removePermissionFromUser(AddPermissionToUser $request   )
    {
        $credentials = $request->validated();

        $user = User::find($credentials['user_id']);
        $permissionName = $request->input('permission_name');

        $user->revokePermissionTo($permissionName);

        return response()->json(['message' =>'The user\'s permission was successfully removed']);
    }
}

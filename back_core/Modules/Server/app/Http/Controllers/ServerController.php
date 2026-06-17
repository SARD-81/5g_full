<?php

namespace Modules\Server\Http\Controllers;

use Spyc;
use Exception;
use Illuminate\Http\Request;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Server\Models\Server;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\User\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Server\Helpers\SshHelper;
use Modules\User\Services\PaginationService;
use phpseclib3\Crypt\EC\Formats\Signature\SSH2;
use App\Http\Controllers\Contract\ApiController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Server\Http\Requests\TestConnectionRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Modules\Server\Http\Requests\Server\EditServerReqest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Modules\Server\Http\Requests\Server\DeleteServerReqest;
use Modules\Server\Http\Requests\Server\CreateServerRequest;
use Modules\Server\Http\Requests\Server\UploadModuleRequest;
use Modules\Server\Http\Requests\Server\StartStopComandReqest;


class ServerController extends ApiController
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function showAllServers(Request $request)
    {

        $serverPermissionsRequest = array_filter(
            Auth::user()->getAllPermissions()->pluck('name')->toArray(),
            fn($permission) => str_starts_with($permission, 'server/')
        );

        $serverPermissionsRequest = array_map(
            fn($permission) => str_replace('server/', '', $permission),
            $serverPermissionsRequest
        );

        return Auth::user()->hasRole('admin')
            ? $this->respondSuccess('Your server list', Server::all())
            : $this->respondSuccess('Your server list', Server::whereIn('name', $serverPermissionsRequest)->get());

    }


    public function giveRoleServerToServer($role, $server, $permission)
    {
        $role->syncPermissions($permission);
        $server->assignRole($role);
    }

    private function givePermissionServerToRoleUsers($permission)
    {
        $roles = Role::whereIn('name', ['expert'])->get();
        foreach ($roles as $role) {
            $users = $role->users;
            foreach ($users as $user)
                $user->givePermissionTo($permission);
        };

        // delete cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function createServer(CreateServerRequest $request)
    {
        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $server = Server::create($credentials);
            $permission = Permission::firstOrCreate(['name' => "server/{$server->name}", 'guard_name' => 'web']);
//                        $role = Role::firstOrCreate(['name' => "server/{$server->name}", 'guard_name' => 'web']);

            // $this->givePermissionServerToRoleUsers($permission);
//                            $this->giveRoleServerToServer($role, $server, $permission);


            activity('create-server')
                ->causedBy(Auth::user())
                ->performedOn($server)
                ->event('create-server')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'createServer',
                    'server' => $server,
                    'server_id' => $server?->id,
                    'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' => Auth::user()->roles()->pluck('name')->first(),
                ])
                ->log('A new server has been created'
                );

            DB::commit();
            return $this->respondCreated('A new server has been created', data: $server);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['msg' => $e->getMessage()], 422);
        }
    }

    public function editServer(EditServerReqest $request)
    {
        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $server = Server::find($credentials['server_id']);
            $serverOldName = $server['name'];
            $permission = Permission::where('name', 'like', 'server/' . $server['name'])->first();


            $server->update($credentials);
            $permission->update(['name' => 'server/' . $credentials['name']]);

            activity('edit-server')
                ->causedBy(Auth::user())
                ->performedOn($server)
                ->event('edit-server')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'editServer',
                    'server' => $server,
                    'server_id' => $server?->id,
                    'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' => Auth::user()->roles()->pluck('name')->first(),
                ])
                ->log('this server edited');


            DB::commit();
            return response()->json(['msg' => 'this server updated', 'server' => $server, 'oldNameServer' => $serverOldName]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['msg' => $e->getMessage()], 422);
        }
    }

    public function deleteServer(DeleteServerReqest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);


        try {
            DB::beginTransaction();

            // validate user authName and Password in delete server
            $user = User::whereRaw('BINARY auth_name = ?', [$credentials['auth_name']])->first();
            if (!$user || !Hash::check($credentials['password'], $user->password))
                return response()->json(['msg' => 'You have entered an incorrect username or password'], 422);


            $server->delete();
            Permission::where('name', "server/{$server->name}")->delete();

            DB::commit();

            activity('delete-server')
                ->causedBy(Auth::user())
                ->performedOn($server)
                ->event('delete')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'deleteServer',
                    'server' => $server,
                    'server_id' => $server?->id,
                    'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' => Auth::user()->roles()->pluck('name')->first(),])
                ->log('The server was successfully deleted');

            return $this->respondSuccess('The server was successfully deleted', $server);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'An issue occurred while deleting all server modules'], 422);
        }
    }


    public function serverStop(StartStopComandReqest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);

        if ($server['is_down'] == Server::OFF)
            return response()->json(['The server is turned off', 422]);


        $server->update(['is_down' => Server::OFF]);
        $server->save();


        activity('server-stop')
            ->causedBy(Auth::user())
            ->performedOn($server)
            ->event('change-status-server')
            ->withProperties([
                'type-log' => 'server',
                'route' => request()->fullUrl(),
                'method' => 'serverStart',
                'server' => $server,
                'server_id' => $server?->id,
                'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                'user_role' => Auth::user()->roles()->pluck('name')->first(),])
            ->log('The server was turned off');


        return $this->respondSuccess('The server was turned off', $server);
    }

    public function serverStart(StartStopComandReqest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);

        if ($server['is_down'] == Server::ON)
            return response()->json(['The server is turned on', 422]);


        $server->update(['is_down' => Server::ON]);
        $server->save();


        activity('server-start')
            ->causedBy(Auth::user())
            ->performedOn($server)
            ->event('change-server-status')
            ->withProperties([
                'type-log' => 'server',
                'route' => request()->fullUrl(),
                'method' => 'serverStart',
                'server' => $server,
                'server_id' => $server?->id,
                'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                'user_role' => Auth::user()->roles()->pluck('name')->first(),
            ])
            ->log('The server has been turned on');


        return $this->respondSuccess('The server has been turned on', $server);
    }

    public function serverStatus(StartStopComandReqest $request)
    {
        $credentials = $request->validated();

        $server = Server::find($credentials['server_id']);
        if (!$server)
            return response()->json(['The server ID is invalid', 404]);


        $status = $server['is_down'] ? 'off' : 'on';

        return response()->json([$status]);
    }


    public function testConnection(TestConnectionRequest $request)
    {
        $creadtional = $request->validated();
        $server = Server::find($creadtional['server_id']);

        // permission
        $serverPermission = 'server/' . $server['name'];

        if (!Auth::user()->hasPermissionTo($serverPermission) && !Auth::user()->hasRole('admin'))
            throw new HttpResponseException(response()->json(['msg' => 'You do not have the Permission to use this server.'], 422));


        // is stop server
        if ($server['is_down'] == Server::OFF) return response()->json(['msg' => 'this off server'], 403);


        try {

//            if (env('SSH_STATUS')) {
                $sshHelper = new sshHelper($server->ip, $creadtional['username'], $creadtional['password'], $creadtional['port'] ?? 22, 7);
                $sshHelper->testConnection();
//            }
            activity('test-connection')
                ->causedBy(Auth::user())
                ->event('test-connection')
                ->withProperties([
                    'type-log' => 'server',
                    'route' => request()->fullUrl(),
                    'method' => 'showConfigModule',
                    'user' => Auth::user()->makeHidden(['roles', 'permissions'])->toArray(),
                    'user_role' => Auth::user()->roles()->pluck('name')->first(),
                    'server' => $server,
                    'server_id' => $server?->id
                ])
                ->log('The connection to the server was successful');

            return response()->json(['msg' => 'connect successful.'], 200);

        } catch (Exception $e) {
            throw $e;
        }
    }
}


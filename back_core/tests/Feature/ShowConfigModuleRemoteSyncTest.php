<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Modules\User\Models\Permission;
use Modules\User\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ShowConfigModuleRemoteSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createUserWithPermissions(array $permissions): User
    {
        $user = User::query()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'auth_name' => 'testuser' . uniqid(),
            'password' => Hash::make('password123'),
            'phone' => '09' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT),
        ]);

        foreach ($permissions as $permissionName) {
            $permission = Permission::findOrCreate($permissionName, 'web');
            $user->givePermissionTo($permission);
        }

        return $user;
    }

    public function test_show_config_module_reads_remote_yaml_and_syncs_pivot_configs(): void
    {
        $user = $this->createUserWithPermissions(['module/read', 'server/Core-Server']);
        Sanctum::actingAs($user);

        $server = Server::query()->create([
            'name' => 'Core-Server',
            'ip' => '10.0.0.15',
            'path_config' => '/tmp/config/',
            'path_run_config' => '/tmp/run/',
            'is_down' => Server::ON,
        ]);

        $module = Module::query()->create([
            'name' => 'MME',
            'service_key' => 'mme',
            'type' => '5gc',
        ]);

        $module->servers()->attach($server->id, [
            'initial_config' => '{"time": 10}',
            'current_config' => '{"time": 10}',
            'previous_config' => null,
        ]);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')->once()->andReturn('__FILE_EXISTS__');
        $sshMock->shouldReceive('getFileContent')->once()->andReturn("time: 20\n");

        $response = $this->postJson("/api/show-config-module/{$server->id}/{$module->id}", [
            'server_id' => $server->id,
            'module_id' => $module->id,
            'username' => 'root',
            'password' => 'secret',
            'port' => 22,
        ]);

        $response->assertOk()->assertJsonPath('config.time', 20);

        $pivot = $module->servers()->where('server_id', $server->id)->first()->pivot;
        $this->assertSame('{"time": 10}', $pivot->previous_config);
        $this->assertSame("{\n    \"time\": 20\n}", $pivot->current_config);
    }

    public function test_show_config_module_returns_error_when_remote_file_missing_and_keeps_db_unchanged(): void
    {
        $user = $this->createUserWithPermissions(['module/read', 'server/Core-Server']);
        Sanctum::actingAs($user);

        $server = Server::query()->create([
            'name' => 'Core-Server',
            'ip' => '10.0.0.16',
            'path_config' => '/tmp/config/',
            'path_run_config' => '/tmp/run/',
            'is_down' => Server::ON,
        ]);

        $module = Module::query()->create([
            'name' => 'MME',
            'service_key' => 'mme',
            'type' => '5gc',
        ]);

        $module->servers()->attach($server->id, [
            'initial_config' => '{"time": 10}',
            'current_config' => '{"time": 10}',
            'previous_config' => '{"time": 5}',
        ]);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')->once()->andReturn('');
        $sshMock->shouldReceive('getFileContent')->never();

        $response = $this->postJson("/api/show-config-module/{$server->id}/{$module->id}", [
            'server_id' => $server->id,
            'module_id' => $module->id,
            'username' => 'root',
            'password' => 'secret',
            'port' => 22,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['config_file']);

        $pivot = $module->servers()->where('server_id', $server->id)->first()->pivot;
        $this->assertSame('{"time": 10}', $pivot->current_config);
        $this->assertSame('{"time": 5}', $pivot->previous_config);
    }
}
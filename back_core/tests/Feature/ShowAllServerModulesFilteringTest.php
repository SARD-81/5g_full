<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ShowAllServerModulesFilteringTest extends TestCase
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

    private function createVisitorUser(): User
    {
        $user = User::query()->create([
            'first_name' => 'List',
            'last_name' => 'Tester',
            'auth_name' => 'listtester' . uniqid(),
            'password' => Hash::make('password123'),
            'phone' => '09' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT),
        ]);

        $role = Role::findOrCreate('visitor', 'web');
        $user->assignRole($role);

        return $user;
    }

    public function test_module_list_excludes_modules_without_remote_config_file(): void
    {
        $user = $this->createVisitorUser();
        Sanctum::actingAs($user);

        $server = Server::query()->create([
            'name' => 'Srv-Filter',
            'ip' => '10.20.30.40',
            'path_config' => '/tmp/config/',
            'path_run_config' => '/tmp/run/',
        ]);

        $moduleExisting = Module::query()->create(['name' => 'Existing', 'service_key' => 'existing', 'type' => '5gc']);
        $moduleMissing = Module::query()->create(['name' => 'Missing', 'service_key' => 'missing', 'type' => 'Epc']);

        $moduleExisting->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);
        $moduleMissing->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->once()->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')->twice()->andReturnUsing(function (string $command) {
            return str_contains($command, 'existing.yaml') ? '__CMD_EXIT__:0' : '__CMD_EXIT__:1';
        });

        $response = $this->postJson('/api/show-all-servies-and-modules/' . $server->id, [
            'server_id' => $server->id,
            'username' => 'root',
            'password' => 'secret',
            'port' => 22,
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data.allModules');
        $response->assertJsonPath('data.allModules.0.id', $moduleExisting->id);
        $response->assertJsonCount(0, 'data.Epc');
        $response->assertJsonCount(1, 'data.5gc');

        $this->assertDatabaseHas('modules', ['id' => $moduleMissing->id]);
        $this->assertDatabaseHas('module_server', ['server_id' => $server->id, 'module_id' => $moduleMissing->id]);
    }

    public function test_module_list_returns_validation_error_when_route_server_and_body_server_mismatch(): void
    {
        $user = $this->createVisitorUser();
        Sanctum::actingAs($user);

        $server = Server::query()->create([
            'name' => 'Srv-Mismatch',
            'ip' => '10.20.30.41',
            'path_config' => '/tmp/config/',
            'path_run_config' => '/tmp/run/',
        ]);

        $otherServer = Server::query()->create([
            'name' => 'Srv-Other',
            'ip' => '10.20.30.42',
            'path_config' => '/tmp/config/',
            'path_run_config' => '/tmp/run/',
        ]);

        $response = $this->postJson('/api/show-all-servies-and-modules/' . $server->id, [
            'server_id' => $otherServer->id,
            'username' => 'root',
            'password' => 'secret',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['server_id']);
    }
}
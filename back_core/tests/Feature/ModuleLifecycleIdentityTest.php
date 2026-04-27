<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Modules\Server\Exceptions\CommandExecutionException;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Modules\User\Models\Permission;
use Modules\User\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ModuleLifecycleIdentityTest extends TestCase
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

    private function createServer(string $name = 'Server-A'): Server
    {
        return Server::query()->create([
            'name' => $name,
            'ip' => '10.0.0.' . random_int(10, 200),
            'path_config' => '/tmp/config/',
            'path_run_config' => '/tmp/run/',
        ]);
    }

    private function mockSshWithOutput(string $output = ''): void
    {
        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')->andReturn($output);
        $sshMock->shouldReceive('getFileContent')->andReturn($output);
        $sshMock->shouldReceive('testConnection')->andReturnTrue();
    }

    public function test_create_validation_uses_servers_payload_shape(): void
    {
        $this->mockSshWithOutput('');
        $user = $this->createUserWithPermissions(['module/create']);
        $server = $this->createServer();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/create-module', [
            'name' => 'PCRF',
            'type' => 'pcrf',
            'config_file' => UploadedFile::fake()->create('module.yaml', 10),
            'servers' => [
                ['id' => $server->id, 'password' => 'secret', 'port' => 22],
            ],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['servers.0.username']);
    }

    public function test_create_rejects_duplicate_technical_identity(): void
    {
        $this->mockSshWithOutput('');
        $user = $this->createUserWithPermissions(['module/create']);
        $server = $this->createServer();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Display Name One',
            'type' => 'pcrf',
            'config_file' => UploadedFile::fake()->create('module.yaml', 10),
            'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
        ];

        $this->postJson('/api/create-module', $payload)->assertOk();

        $this->postJson('/api/create-module', array_merge($payload, ['name' => 'Different Display Name']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_create_accepts_each_allowed_module_type(): void
    {
        $this->mockSshWithOutput('');
        $user = $this->createUserWithPermissions(['module/create']);
        $server = $this->createServer('Srv-Allowed-Types');
        Sanctum::actingAs($user);

        foreach (['hss', 'pcrf', 'upf', 'sgwc', 'sgwu', 'smf', 'mme'] as $index => $type) {
            $this->postJson('/api/create-module', [
                'name' => 'Display ' . $type . ' ' . $index,
                'type' => $type,
                'config_file' => UploadedFile::fake()->create('module.yaml', 10),
                'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
            ])->assertOk();

            $this->assertDatabaseHas('modules', ['name' => 'Display ' . $type . ' ' . $index, 'service_key' => $type, 'type' => $type]);
        }
    }

    public function test_create_rejects_disallowed_module_types(): void
    {
        $this->mockSshWithOutput('');
        $user = $this->createUserWithPermissions(['module/create']);
        $server = $this->createServer('Srv-Bad-Types');
        Sanctum::actingAs($user);

        foreach (['EPC', '5GC', 'hss,pcrf', 'custom-type', 'HSS'] as $invalidType) {
            $this->postJson('/api/create-module', [
                'name' => 'Display ' . $invalidType,
                'type' => $invalidType,
                'config_file' => UploadedFile::fake()->create('module.yaml', 10),
                'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
            ])->assertStatus(422)->assertJsonValidationErrors(['type']);
        }
    }

    public function test_create_uses_type_for_service_key_and_remote_artifacts(): void
    {
        $user = $this->createUserWithPermissions(['module/create']);
        $server = $this->createServer('Srv-Type-Identity');
        Sanctum::actingAs($user);

        $commands = [];
        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->andReturnUsing(function ($command) use (&$commands) {
                $commands[] = $command;
                return '';
            });
        $sshMock->shouldReceive('getFileContent')->andReturn('');
        $sshMock->shouldReceive('testConnection')->andReturnTrue();

        $this->postJson('/api/create-module', [
            'name' => 'hss12',
            'type' => 'hss',
            'config_file' => UploadedFile::fake()->create('module.yaml', 10),
            'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
        ])->assertOk();

        $module = Module::query()->where('name', 'hss12')->firstOrFail();
        $this->assertSame('hss', $module->service_key);
        $this->assertSame('bbdh-hssd', \Modules\Server\Utility\ModuleIdentity::serviceUnitName($module));
        $this->assertContains("echo '' > /tmp/config/hss.yaml", $commands);
        $this->assertNotContains("echo '' > /tmp/config/hss12.yaml", $commands);
    }

    public function test_edit_rename_changes_display_name_not_service_key(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'PCRF', 'service_key' => 'pcrf', 'type' => '5gc']);
        $server = $this->createServer('Srv-Rename');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'name' => 'PCRF 2',
            'type' => '5gc',
        ])->assertOk();

        $module->refresh();
        $this->assertSame('PCRF 2', $module->name);
        $this->assertSame('pcrf', $module->service_key);
    }

    public function test_start_uses_service_key_not_display_name(): void
    {
        $this->assertServiceCommandUsesServiceKey('start-service-config', 'systemctl start bbdh-pcrf-keyd');
    }

    public function test_stop_uses_service_key_not_display_name(): void
    {
        $this->assertServiceCommandUsesServiceKey('stop-service-config', 'systemctl stop bbdh-pcrf-keyd');
    }

    public function test_restart_uses_service_key_not_display_name(): void
    {
        $this->assertServiceCommandUsesServiceKey('restart-service-config', 'systemctl restart bbdh-pcrf-keyd');
    }

    public function test_status_uses_service_key_not_display_name(): void
    {
        $this->assertServiceCommandUsesServiceKey('status-service-config', 'systemctl status bbdh-pcrf-keyd');
    }

    private function assertServiceCommandUsesServiceKey(string $endpoint, string $expectedCommand): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'Renamed Display', 'service_key' => 'pcrf-key', 'type' => '5gc']);
        $server = $this->createServer('Srv-Command-' . uniqid());

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->once()
            ->withArgs(function ($command) use ($expectedCommand) {
                return $command === $expectedCommand;
            })
            ->andReturn('ok');

        $this->postJson('/api/' . $endpoint, [
            'module_id' => $module->id,
            'server_id' => $server->id,
            'username' => 'u',
            'password' => 'p',
            'port' => 22,
        ])->assertOk();
    }

    private function makeServicePayload(Module $module, Server $server): array
    {
        return [
            'module_id' => $module->id,
            'server_id' => $server->id,
            'username' => 'u',
            'password' => 'p',
            'port' => 22,
        ];
    }

    public function test_wrong_ssh_credentials_returns_ssh_login_failed_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'MME', 'service_key' => 'mme', 'type' => '5gc']);
        $server = $this->createServer('Srv-Auth-Fail');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')
            ->andThrow(new CommandExecutionException('ssh_login_failed', 'SSH login failed. Username/password/port is invalid.', [], 401));

        $this->postJson('/api/start-service-config', $this->makeServicePayload($module, $server))
            ->assertStatus(401)
            ->assertJsonPath('error_code', 'ssh_login_failed');
    }

    public function test_ssh_transport_failure_returns_ssh_connection_failed_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'AMF', 'service_key' => 'amf', 'type' => '5gc']);
        $server = $this->createServer('Srv-Conn-Fail');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')
            ->andThrow(new CommandExecutionException('ssh_connection_failed', 'Unable to establish SSH connection to remote server.', [], 502));

        $this->postJson('/api/start-service-config', $this->makeServicePayload($module, $server))
            ->assertStatus(502)
            ->assertJsonPath('error_code', 'ssh_connection_failed');
    }

    public function test_sudo_failure_returns_sudo_failed_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'SMF', 'service_key' => 'smf', 'type' => '5gc']);
        $server = $this->createServer('Srv-Sudo-Fail');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->andThrow(new CommandExecutionException('sudo_failed', 'SSH connected, but sudo/systemctl execution failed.', [], 422));

        $this->postJson('/api/start-service-config', $this->makeServicePayload($module, $server))
            ->assertStatus(422)
            ->assertJsonPath('error_code', 'sudo_failed');
    }

    public function test_missing_remote_service_unit_returns_service_not_found_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'MME', 'service_key' => 'mme', 'type' => '5gc']);
        $server = $this->createServer('Srv-Service-Missing');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->andReturn("Unit bbdh-mmed.service could not be found.\n__CMD_EXIT__:5");

        $this->postJson('/api/start-service-config', $this->makeServicePayload($module, $server))
            ->assertStatus(422)
            ->assertJsonPath('error_code', 'service_not_found');
    }

    public function test_systemctl_failure_returns_service_command_failed_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'MME', 'service_key' => 'mme', 'type' => '5gc']);
        $server = $this->createServer('Srv-Systemctl-Fail');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->andReturn("Failed to start bbdh MME Daemon.\n__CMD_EXIT__:1");

        $this->postJson('/api/start-service-config', $this->makeServicePayload($module, $server))
            ->assertStatus(422)
            ->assertJsonPath('error_code', 'service_command_failed');
    }

    public function test_timeout_returns_command_timeout_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'NSSF', 'service_key' => 'nssf', 'type' => '5gc']);
        $server = $this->createServer('Srv-Timeout');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->andThrow(new CommandExecutionException('command_timeout', 'Remote command timed out before completion.', [], 504));

        $this->postJson('/api/start-service-config', $this->makeServicePayload($module, $server))
            ->assertStatus(504)
            ->assertJsonPath('error_code', 'command_timeout');
    }

    public function test_missing_credentials_returns_validation_failed_code(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);
        $module = Module::query()->create(['name' => 'MME', 'service_key' => 'mme', 'type' => '5gc']);
        $server = $this->createServer('Srv-Validation');
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $this->postJson('/api/start-service-config', [
            'module_id' => $module->id,
            'server_id' => $server->id,
        ])->assertStatus(422)
            ->assertJsonPath('error_code', 'validation_failed');
    }

    public function test_duplicate_create_does_not_overwrite_existing_yaml_target(): void
    {
        $this->mockSshWithOutput('');
        $user = $this->createUserWithPermissions(['module/create']);
        $server = $this->createServer('Srv-Dupe');
        Sanctum::actingAs($user);

        $this->postJson('/api/create-module', [
            'name' => 'PCRF',
            'type' => 'pcrf',
            'config_file' => UploadedFile::fake()->create('module.yaml', 10),
            'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
        ])->assertOk();

        $this->postJson('/api/create-module', [
            'name' => 'pcrf second',
            'type' => 'pcrf',
            'config_file' => UploadedFile::fake()->create('module.yaml', 10),
            'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
        ])->assertStatus(422);

        $this->assertSame(1, Module::query()->where('service_key', 'pcrf')->count());
    }

    public function test_delete_cleans_remote_artifacts_before_database_delete(): void
    {
        $user = $this->createUserWithPermissions(['module/delete']);
        $server = $this->createServer('Srv-Delete');
        $user->givePermissionTo(Permission::findOrCreate('server/' . $server->name, 'web'));
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'PCRF', 'service_key' => 'pcrf', 'type' => '5gc']);
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')->times(7)->andReturn('');

        $this->deleteJson('/api/delete-module', [
            'module_id' => $module->id,
            'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
        ])->assertOk();

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }

    public function test_delete_cleanup_uses_service_key_for_yaml_and_run_config_artifacts(): void
    {
        $user = $this->createUserWithPermissions(['module/delete']);
        $server = $this->createServer('Srv-Delete-Identity');
        $user->givePermissionTo(Permission::findOrCreate('server/' . $server->name, 'web'));
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'Display Module', 'service_key' => 'stable-key', 'type' => '5gc']);
        $module->servers()->attach($server->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $commands = [];
        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->times(7)
            ->andReturnUsing(function ($command) use (&$commands) {
                $commands[] = $command;
                return '';
            });

        $this->deleteJson('/api/delete-module', [
            'module_id' => $module->id,
            'servers' => [['id' => $server->id, 'username' => 'u', 'password' => 'p', 'port' => 22]],
        ])->assertOk();

        $this->assertContains("rm -f '/tmp/config/stable-key.yaml'", $commands);
        $this->assertContains("rm -f '/tmp/run/stable-key.yaml'", $commands);
        $this->assertContains("rm -f '/tmp/run/bbdh-stable-keyd'", $commands);
        $this->assertContains("rm -f '/tmp/run/bbdh-stable-keyd.service'", $commands);
        $this->assertContains("rm -f '/tmp/run/bbdh-stable-keyd.sh'", $commands);
    }

    public function test_edit_remove_server_cleans_remote_before_detach(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        $serverA = $this->createServer('Srv-Edit-A');
        $serverB = $this->createServer('Srv-Edit-B');
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverA->name, 'web'));
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverB->name, 'web'));
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'PCRF', 'service_key' => 'stable-edit', 'type' => '5gc']);
        $module->servers()->attach($serverA->id, ['initial_config' => '{}', 'current_config' => '{}']);
        $module->servers()->attach($serverB->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $commands = [];
        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->times(7)
            ->andReturnUsing(function ($command) use (&$commands) {
                $commands[] = $command;
                return '';
            });

        $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'servers' => [
                ['id' => $serverA->id, 'username' => 'u-a', 'password' => 'p-a', 'port' => 22],
            ],
            'servers_to_remove' => [
                ['id' => $serverB->id, 'username' => 'u-b', 'password' => 'p-b', 'port' => 22],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('module_server', ['module_id' => $module->id, 'server_id' => $serverA->id]);
        $this->assertDatabaseMissing('module_server', ['module_id' => $module->id, 'server_id' => $serverB->id]);
        $this->assertContains("rm -f '/tmp/run/bbdh-stable-editd'", $commands);
    }

    public function test_edit_remove_server_cleanup_failure_prevents_detach(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        $serverA = $this->createServer('Srv-Edit-Fail-A');
        $serverB = $this->createServer('Srv-Edit-Fail-B');
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverA->name, 'web'));
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverB->name, 'web'));
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'PCRF', 'service_key' => 'stable-fail', 'type' => '5gc']);
        $module->servers()->attach($serverA->id, ['initial_config' => '{}', 'current_config' => '{}']);
        $module->servers()->attach($serverB->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->once()
            ->andReturn('Permission denied');

        $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'servers' => [
                ['id' => $serverA->id, 'username' => 'u-a', 'password' => 'p-a', 'port' => 22],
            ],
            'servers_to_remove' => [
                ['id' => $serverB->id, 'username' => 'u-b', 'password' => 'p-b', 'port' => 22],
            ],
        ])->assertStatus(422);

        $this->assertDatabaseHas('module_server', ['module_id' => $module->id, 'server_id' => $serverA->id]);
        $this->assertDatabaseHas('module_server', ['module_id' => $module->id, 'server_id' => $serverB->id]);
    }

    public function test_edit_remove_server_requires_credentials_for_removed_servers(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        $serverA = $this->createServer('Srv-Edit-Validation-A');
        $serverB = $this->createServer('Srv-Edit-Validation-B');
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverA->name, 'web'));
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverB->name, 'web'));
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'PCRF', 'service_key' => 'stable-validation', 'type' => '5gc']);
        $module->servers()->attach($serverA->id, ['initial_config' => '{}', 'current_config' => '{}']);
        $module->servers()->attach($serverB->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'servers' => [
                ['id' => $serverA->id, 'username' => 'u-a', 'password' => 'p-a', 'port' => 22],
            ],
        ])->assertStatus(422)->assertJsonValidationErrors(['servers_to_remove']);
    }

    public function test_edit_remove_server_cleanup_uses_service_key_after_display_name_change(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        $serverA = $this->createServer('Srv-Edit-Rename-A');
        $serverB = $this->createServer('Srv-Edit-Rename-B');
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverA->name, 'web'));
        $user->givePermissionTo(Permission::findOrCreate('server/' . $serverB->name, 'web'));
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'Before Rename', 'service_key' => 'immutable-key', 'type' => '5gc']);
        $module->update(['name' => 'After Rename']);
        $module->servers()->attach($serverA->id, ['initial_config' => '{}', 'current_config' => '{}']);
        $module->servers()->attach($serverB->id, ['initial_config' => '{}', 'current_config' => '{}']);

        $commands = [];
        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->times(7)
            ->andReturnUsing(function ($command) use (&$commands) {
                $commands[] = $command;
                return '';
            });

        $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'servers' => [
                ['id' => $serverA->id, 'username' => 'u-a', 'password' => 'p-a', 'port' => 22],
            ],
            'servers_to_remove' => [
                ['id' => $serverB->id, 'username' => 'u-b', 'password' => 'p-b', 'port' => 22],
            ],
            'name' => 'Display Name Changed Again',
        ])->assertOk();

        $this->assertContains("rm -f '/tmp/config/immutable-key.yaml'", $commands);
        $this->assertContains("rm -f '/tmp/run/bbdh-immutable-keyd'", $commands);
    }

    public function test_create_then_rename_does_not_break_start_command(): void
    {
        $user = $this->createUserWithPermissions(['module/update']);
        Sanctum::actingAs($user);

        $module = Module::query()->create(['name' => 'PCRF', 'service_key' => 'pcrf', 'type' => '5gc']);
        $server = $this->createServer('Srv-Rename-Start');
        $module->update(['name' => 'PCRF2']);

        $sshMock = Mockery::mock('overload:Modules\\Server\\Helpers\\SshHelper');
        $sshMock->shouldReceive('__construct')->andReturnNull();
        $sshMock->shouldReceive('runCommandModule')
            ->once()
            ->withArgs(fn ($command) => $command === 'systemctl start bbdh-pcrfd')
            ->andReturn('ok');

        $this->postJson('/api/start-service-config', [
            'module_id' => $module->id,
            'server_id' => $server->id,
            'username' => 'u',
            'password' => 'p',
            'port' => 22,
        ])->assertOk();
    }

    public function test_service_key_is_backfilled_for_existing_record_creation_path(): void
    {
        $module = Module::query()->create(['name' => 'PCRF Existing', 'type' => '5gc']);
        $this->assertSame('pcrf-existing', $module->service_key);
    }
}

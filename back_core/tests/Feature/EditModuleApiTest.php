<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Models\User;
use Modules\User\Models\Permission;
use Modules\Server\Models\Module;
use Modules\Server\Models\Server;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

class EditModuleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }


    private function createUser(): User
    {
        return User::query()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'auth_name' => 'testuser'.uniqid(),
            'password' => Hash::make('password123'),
            'phone' => '09'.str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT),
        ]);
    }

    public function test_edit_module_updates_module_in_database(): void
    {
        $permission = Permission::findOrCreate('module/update', 'web');

        $user = $this->createUser();
        $user->givePermissionTo($permission);

        $module = Module::query()->create([
            'name' => 'Old Module Name',
            'type' => 'epc',
        ]);

        $server = Server::query()->create([
            'name' => 'Server-Edit-Metadata',
            'ip' => '10.0.0.11',
            'path_config' => '/tmp/config',
            'path_run_config' => '/tmp/run',
        ]);

        $module->servers()->attach($server->id, [
            'initial_config' => '{"meta":"v"}',
            'current_config' => '{"meta":"v"}',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'name' => 'Updated Module Name',
            'type' => '5gc',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('module.module_id', $module->id)
            ->assertJsonPath('module.module_name', 'Updated Module Name')
            ->assertJsonPath('module.module_type', '5gc');

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'name' => 'Updated Module Name',
            'type' => '5gc',
        ]);
    }

    public function test_edit_module_requires_permission(): void
    {
        $user = $this->createUser();
        $module = Module::query()->create([
            'name' => 'Old Module Name',
            'type' => 'epc',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/edit-module', [
            'module_id' => $module->id,
            'name' => 'Should Not Persist',
            'type' => '5gc',
        ])->assertForbidden();

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'name' => 'Old Module Name',
            'type' => 'epc',
        ]);
    }

    public function test_edit_module_returns_validation_error_for_invalid_payload(): void
    {
        $permission = Permission::findOrCreate('module/update', 'web');

        $user = $this->createUser();
        $user->givePermissionTo($permission);

        Sanctum::actingAs($user);

        $this->postJson('/api/edit-module', [
            'module_id' => 999999,
            'name' => 'Updated Module Name',
            'type' => '5gc',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['module_id']);
    }

    public function test_create_module_returns_validation_error_when_server_username_missing(): void
    {
        $permission = Permission::findOrCreate('module/create', 'web');

        $user = $this->createUser();
        $user->givePermissionTo($permission);

        $server = Server::query()->create([
            'name' => 'Server-A',
            'ip' => '10.0.0.10',
            'path_config' => '/tmp/config',
            'path_run_config' => '/tmp/run',
        ]);

        Sanctum::actingAs($user);

        $response = $this->withHeader('Accept', 'application/json')->post('/api/create-module', [
            'name' => 'My Module',
            'type' => '5gc',
            'config_file' => UploadedFile::fake()->create('module.yaml', 10, 'application/x-yaml'),
            'servers' => [
                [
                    'id' => $server->id,
                    'password' => 'secret-password',
                    'port' => 22,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['servers.0.username']);
    }

}
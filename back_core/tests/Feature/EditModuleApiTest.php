<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Models\User;
use Modules\User\Models\Permission;
use Modules\Server\Models\Module;
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

    public function test_edit_module_updates_module_in_database(): void
    {
        $permission = Permission::findOrCreate('module/update', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $module = Module::query()->create([
            'name' => 'Old Module Name',
            'type' => 'epc',
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
        $user = User::factory()->create();
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

        $user = User::factory()->create();
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
}

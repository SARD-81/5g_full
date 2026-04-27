<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ModuleScheduleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::query()->create([
            'first_name' => 'Schedule',
            'last_name' => 'Tester',
            'auth_name' => 'schedule_' . uniqid(),
            'password' => Hash::make('password123'),
            'phone' => '09' . str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT),
        ]);

        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    public function test_expert_can_read_module_schedule_index(): void
    {
        $user = $this->createUserWithRole(Role::EXPERT);
        Sanctum::actingAs($user);

        $this->getJson('/api/module/schedule')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_expert_cannot_create_module_schedule(): void
    {
        $user = $this->createUserWithRole(Role::EXPERT);
        Sanctum::actingAs($user);

        $this->postJson('/api/module/schedule', [])
            ->assertForbidden();
    }
}

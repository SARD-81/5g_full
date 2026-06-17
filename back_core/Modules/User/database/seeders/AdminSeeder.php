<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [

                // server
            'VM/read',
            'VM/create',
            'VM/update',
            'VM/delete',

                // module
            'module/read',
            'module/create',
            'module/update',
            'module/delete',

                // on or off server
            'VM/status',

                // subscriber
            'subscriber',

                // monitoring
            'monitoring',

                // log
            'log',

               // user
            'user',

            // kpi
            'kpi',

            // route
            'route',

            // ping
            'ping',


            // trace
            'trace',
        ];



        foreach ($permissions as $permission)
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);



        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $allPermissions = Permission::all();

        $adminRole->syncPermissions($allPermissions);


        $admin = User::firstOrCreate(
            ['auth_name' => 'ownerApp'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'phone' => '09120000000',
                'password' => Hash::make('password'),
            ],
        );

        $admin->assignRole($adminRole);





            // visitor
    $visitorRole = Role::firstOrCreate(['name' => 'visitor']);
        //  $permissions = Permission::whereIn('name', ['VM/read', 'module/read', 'monitoring'])->get();
        //  $visitorRole->syncPermissions($permissions);


            // expert
        $visitorRole = Role::firstOrCreate(['name' => 'expert']);
        // $permissions = Permission::whereIn('name', [
        //     'module/read',
        //     'module/create',
        //     'module/update',
        //     'module/delete',

        //     'VM/read',
        //     'VM/create',
        //     'VM/update',
        //     'VM/delete',

        //     'monitoring'

        //     ])->get();
        // $visitorRole->syncPermissions($permissions);


    }
}

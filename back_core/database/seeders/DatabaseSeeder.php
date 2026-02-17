<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Modules\SystemSetting\Database\Seeders\SystemSettingDatabaseSeeder;
use Modules\User\Database\Seeders\AdminSeeder;
use Modules\User\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            AdminSeeder::class,
        );
        $this->call(
            SystemSettingDatabaseSeeder::class
        );
    }
}

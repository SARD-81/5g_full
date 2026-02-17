<?php

namespace Modules\SystemSetting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SystemSetting\Models\SystemSettings;

class SystemSettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!SystemSettings::exists())
            SystemSettings::create();

    }
}

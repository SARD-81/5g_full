<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('modules', 'service_key')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE modules DROP INDEX IF EXISTS modules_service_key_unique');
            return;
        }

        Schema::table('modules', function (Blueprint $table) {
            $table->dropUnique('modules_service_key_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('modules', 'service_key')) {
            return;
        }

        Schema::table('modules', function (Blueprint $table) {
            $table->unique('service_key');
        });
    }
};

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
            $schema = DB::getDatabaseName();
            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', $schema)
                ->where('table_name', 'modules')
                ->where('index_name', 'modules_service_key_unique')
                ->exists();

            if ($indexExists) {
                DB::statement('ALTER TABLE modules DROP INDEX modules_service_key_unique');
            }
            return;
        }

        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'service_key')) {
                $table->dropUnique('modules_service_key_unique');
            }
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

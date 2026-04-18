<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Server\Utility\ModuleIdentity;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->string('service_key', 60)->nullable()->after('name');
        });

        $collisions = [];
        DB::table('modules')->select(['id', 'name'])->orderBy('id')->chunkById(100, function ($modules) use (&$collisions) {
            $usedKeys = [];
            foreach ($modules as $module) {
                $key = ModuleIdentity::normalizeKey($module->name);

                if (isset($usedKeys[$key])) {
                    $collisions[$key] = [$usedKeys[$key], $module->id];
                    continue;
                }

                $duplicateInDb = DB::table('modules')
                    ->where('id', '!=', $module->id)
                    ->where('service_key', $key)
                    ->exists();

                if ($duplicateInDb) {
                    $existingId = DB::table('modules')->where('service_key', $key)->value('id');
                    $collisions[$key] = [$existingId, $module->id];
                    continue;
                }

                $usedKeys[$key] = $module->id;

                DB::table('modules')
                    ->where('id', $module->id)
                    ->update(['service_key' => $key]);
            }
        });

        if (!empty($collisions)) {
            $message = collect($collisions)
                ->map(fn ($ids, $key) => sprintf('%s => [%s]', $key, implode(', ', $ids)))
                ->implode('; ');

            throw new RuntimeException('Migration halted due to module service_key collisions: ' . $message);
        }

        Schema::table('modules', function (Blueprint $table) {
            $table->string('service_key', 60)->nullable(false)->change();
            $table->unique('service_key');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropUnique('modules_service_key_unique');
            $table->dropColumn('service_key');
        });
    }
};

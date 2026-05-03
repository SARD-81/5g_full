<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules')
            ->whereNotIn('id', function ($query) {
                $query->select('module_id')->from('module_server');
            })
            ->delete();
    }

    public function down(): void
    {
        // Intentionally empty.
        // Orphan modules cannot be restored safely because their old server relations are unknown.
    }
};
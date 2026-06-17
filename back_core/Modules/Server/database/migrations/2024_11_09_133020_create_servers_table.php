<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();

            $table->string('path_config')->default('/home/siz-tel/bbdh-2.6.6-noCg/install/etc/bbdh/');
            $table->string('path_run_config')->default('/home/siz-tel/bbdh-2.6.6-noCg/install/bin/');

            $table->string('ip')->unique();
            $table->boolean('is_down')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};

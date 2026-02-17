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
        Schema::create('backup_histories', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();

            $table->json('servers')->nullable();
            $table->string('destination_path')->nullable();

            $table->tinyInteger('status')->default(0)->comment('0:runing 1:success 2:failed');

            $table->foreignId('backup_config_id')
                ->nullable()
                ->constrained('backup_configs')
                ->onUpdate('set null')
                ->onDelete('set null');

            $table->string('message')->nullable();

            $table->timestamp('start_time')->nullable();
            $table->timestamp('finish_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_histories');
    }
};

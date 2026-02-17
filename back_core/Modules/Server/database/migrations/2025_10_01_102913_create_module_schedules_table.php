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
        Schema::create('module_schedules', function (Blueprint $table) {
            $table->id();

            $table->text('config');

            $table->foreignId('module_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->tinyInteger('status')
                ->default(0)
                ->comment('0 => waiting, 1 => running, 2 => failed, 3 => success');

            $table->foreignId('server_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('username_ssh');
            $table->string('password_ssh');
            $table->string('port_ssh');

            $table->timestamp('run_scheduled_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_schedules');
    }
};

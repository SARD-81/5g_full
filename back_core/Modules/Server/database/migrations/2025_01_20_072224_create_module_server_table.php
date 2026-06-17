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
        Schema::create('module_server', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')
                    ->constrained()
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->foreignId('module_id')
                    ->constrained()
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                            // config module
                    $table->text('initial_config')->nullable()->comment('config module');
                    $table->text('previous_config')->nullable()->comment('config module');
                    $table->text('current_config')->nullable()->comment('config module');


            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_server');
    }
};

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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company');
            $table->string('person');
            $table->string('site')->nullable();
            $table->string('store')->nullable();
            $table->string('work_type');
            $table->string('task_type');
            $table->text('request_detail')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->string('visit_status');
            $table->string('repair_place')->nullable();
            $table->text('visit_status_detail')->nullable();
            $table->text('work_detail')->nullable();
            $table->text('signature')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
}; 
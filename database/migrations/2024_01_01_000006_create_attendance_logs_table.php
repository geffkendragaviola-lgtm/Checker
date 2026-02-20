<?php
// database/migrations/2024_01_01_000006_create_attendance_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code');
            $table->timestamp('log_datetime');
            $table->enum('log_type', ['IN', 'OUT']);
            $table->string('source_file')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['employee_code', 'log_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
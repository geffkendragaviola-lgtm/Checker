<?php
// database/migrations/2024_01_01_000007_create_attendance_records_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->date('attendance_date');
            $table->foreignId('schedule_id')->constrained('work_schedules');
            $table->time('time_in_am')->nullable();
            $table->time('time_out_lunch')->nullable();
            $table->time('time_in_pm')->nullable();
            $table->time('time_out_pm')->nullable();
            $table->integer('late_minutes_am')->default(0);
            $table->integer('late_minutes_pm')->default(0);
            $table->integer('total_late_minutes')->default(0);
            $table->decimal('workday_rendered', 3, 2)->default(0);
            $table->boolean('missing_logs')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
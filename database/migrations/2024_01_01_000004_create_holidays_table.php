<?php
// database/migrations/2024_01_01_000004_create_holidays_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('holiday_date');
            $table->enum('type', ['regular', 'special', 'local'])->default('regular');
            $table->foreignId('schedule_id')->nullable()->constrained('work_schedules');
            $table->decimal('rate_multiplier', 5, 2)->default(1.0);
            $table->boolean('is_paid')->default(true);
            $table->foreignId('department_id')->nullable()->constrained(); // For branch-specific holidays
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
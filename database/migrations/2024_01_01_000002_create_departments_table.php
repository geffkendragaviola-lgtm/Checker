<?php
// database/migrations/2024_01_01_000002_create_departments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('branch_code')->nullable(); // Keep this if you need it
            $table->foreignId('schedule_id')->constrained('work_schedules');
            $table->string('description')->nullable(); // Add this if needed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
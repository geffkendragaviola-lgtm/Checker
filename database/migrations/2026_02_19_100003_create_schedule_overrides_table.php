<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('schedule_overrides')) {
            return;
        }

        Schema::create('schedule_overrides', function (Blueprint $table) {
            $table->id();
            $table->date('override_date');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('schedule_id')->constrained('work_schedules');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_overrides');
    }
};


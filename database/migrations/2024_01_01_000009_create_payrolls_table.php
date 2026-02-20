<?php
// database/migrations/2024_01_01_000009_create_payrolls_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->decimal('days_worked', 5, 2)->default(0);
            $table->decimal('late_deductions', 10, 2)->default(0);
            $table->decimal('absent_deductions', 10, 2)->default(0);
            $table->decimal('holiday_pay', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('gross_pay', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate payroll per employee per period
            $table->unique(['payroll_period_id', 'employee_id'], 'unique_payroll_per_employee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
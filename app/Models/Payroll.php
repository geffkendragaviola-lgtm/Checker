<?php
// app/Models/Payroll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'days_worked',
        'late_deductions',
        'absent_deductions',
        'holiday_pay',
        'overtime_pay',
        'gross_pay',
        'total_deductions',
        'net_pay',
        'notes'
    ];

    protected $casts = [
        'days_worked' => 'decimal:2',
        'late_deductions' => 'decimal:2',
        'absent_deductions' => 'decimal:2',
        'holiday_pay' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2'
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollItems()
    {
        return $this->hasMany(PayrollItem::class);
    }
}
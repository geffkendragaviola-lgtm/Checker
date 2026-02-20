<?php
// app/Models/Employee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'middle_name',
        'department_id',
        'position',
        'daily_rate',
        'hire_date',
        'employment_status',
        'email',
        'phone',
        'address'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'daily_rate' => 'decimal:2'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'employee_code', 'employee_code');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
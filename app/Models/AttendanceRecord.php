<?php
// app/Models/AttendanceRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'schedule_id',
        'time_in_am',
        'time_out_lunch',
        'time_in_pm',
        'time_out_pm',
        'late_minutes_am',
        'late_minutes_pm',
        'total_late_minutes',
        'workday_rendered',
        'missing_logs',
        'remarks'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'time_in_am' => 'datetime',
        'time_out_lunch' => 'datetime',
        'time_in_pm' => 'datetime',
        'time_out_pm' => 'datetime',
        'workday_rendered' => 'decimal:2',
        'missing_logs' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }

    public function getIsLateAttribute()
    {
        return $this->total_late_minutes > 0;
    }
}
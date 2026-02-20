<?php
// app/Models/WorkSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'work_start_time',
        'work_end_time',
        'break_start_time',
        'break_end_time',
        'grace_period_minutes',
        'is_working_day'
    ];

    protected $casts = [
        'work_start_time' => 'datetime',
        'work_end_time' => 'datetime',
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
        'is_working_day' => 'boolean'
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function scheduleOverrides()
    {
        return $this->hasMany(ScheduleOverride::class);
    }
}
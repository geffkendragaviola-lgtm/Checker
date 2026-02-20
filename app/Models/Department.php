<?php
// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'branch_code',
        'schedule_id'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }

    public function scheduleOverrides()
    {
        return $this->hasMany(ScheduleOverride::class);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }
}
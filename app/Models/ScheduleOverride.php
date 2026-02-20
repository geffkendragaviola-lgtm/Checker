<?php
// app/Models/ScheduleOverride.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'override_date',
        'department_id',
        'schedule_id',
        'reason',
        'is_global'
    ];

    protected $casts = [
        'override_date' => 'date',
        'is_global' => 'boolean'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }
}
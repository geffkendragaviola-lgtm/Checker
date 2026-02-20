<?php
// app/Models/Holiday.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'holiday_date',
        'type',
        'schedule_id',
        'rate_multiplier',
        'is_paid',
        'department_id'
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'rate_multiplier' => 'decimal:2',
        'is_paid' => 'boolean'
    ];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
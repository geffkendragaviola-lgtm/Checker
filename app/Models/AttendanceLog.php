<?php
// app/Models/AttendanceLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'log_datetime',
        'log_type',
        'source_file',
        'is_processed'
    ];

    protected $casts = [
        'log_datetime' => 'datetime',
        'is_processed' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_code', 'employee_code');
    }
}
<?php
// app/Models/PayrollPeriod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'cutoff_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'cutoff_date' => 'date'
    ];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
<?php
// app/Models/PayrollItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'type',
        'category',
        'amount',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function scopeAdditions($query)
    {
        return $query->where('type', 'addition');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }
}
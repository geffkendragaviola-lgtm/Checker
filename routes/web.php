<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Dashboard Route - Using Controller
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Dashboard Attendance Data API
Route::get('/dashboard/attendance-data', [DashboardController::class, 'getAttendanceData'])
    ->middleware(['auth'])
    ->name('dashboard.attendance-data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/upload', [AttendanceController::class, 'upload'])->name('attendance.upload');
    
    // Holiday Routes
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::get('/holidays/data', [HolidayController::class, 'getData'])->name('holidays.data');
    Route::get('/holidays/stats', [HolidayController::class, 'getStats'])->name('holidays.stats');
    Route::get('/holidays/{id}', [HolidayController::class, 'show'])->name('holidays.show');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/holidays/{id}', [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    // Payroll Routes
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/employees', [PayrollController::class, 'getEmployees'])->name('payroll.employees');
    Route::get('/payroll/deductions/summary', [PayrollController::class, 'getDeductionSummary'])->name('payroll.deductions.summary');
    Route::get('/payroll/employee/{id}', [PayrollController::class, 'getEmployeePayroll'])->name('payroll.employee');
    Route::post('/payroll/periods', [PayrollController::class, 'createPeriod'])->name('payroll.periods.create');
    Route::post('/payroll/process', [PayrollController::class, 'process'])->name('payroll.process');
    Route::post('/payroll/settings', [PayrollController::class, 'saveSettings'])->name('payroll.settings');

    // Employee Routes
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');

    // Schedule Routes
    Route::resource('schedules', ScheduleController::class);

    // Report Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
    Route::get('/reports/holidays', [ReportController::class, 'holidays'])->name('reports.holidays');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';
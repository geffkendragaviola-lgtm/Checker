<?php
// routes/web.php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', function () {
        return Inertia::render('Attendance/Records');
    })->name('admin.attendance.index');

    Route::get('/attendance/upload-logs', function () {
        return Inertia::render('Attendance/UploadLogs');
    })->name('admin.attendance.upload-logs');

    Route::get('/attendance/records', function () {
        return Inertia::render('Attendance/Records');
    })->name('admin.attendance.records');

    Route::get('/attendance/summary', function () {
        return Inertia::render('Attendance/Summary');
    })->name('admin.attendance.summary');

    Route::get('/attendance/missing-logs', function () {
        return Inertia::render('Attendance/MissingLogs');
    })->name('admin.attendance.missing-logs');

    Route::get('/letters', function () {
        return Inertia::render('Letters/Index');
    })->name('admin.letters.index');

    Route::get('/letters/generate-notice', function () {
        return Inertia::render('Letters/GenerateNotice');
    })->name('admin.letters.generate-notice');

    Route::get('/letters/history', function () {
        return Inertia::render('Letters/History');
    })->name('admin.letters.history');

    Route::get('/payroll', function () {
        return Inertia::render('Payroll/Index');
    })->name('admin.payroll.index');

    Route::get('/payroll/periods', function () {
        return Inertia::render('Payroll/Periods');
    })->name('admin.payroll.periods');

    Route::get('/payroll/generate', function () {
        return Inertia::render('Payroll/Generate');
    })->name('admin.payroll.generate');

    Route::get('/payroll/summary', function () {
        return Inertia::render('Payroll/Summary');
    })->name('admin.payroll.summary');

    Route::get('/payroll/payslips', function () {
        return Inertia::render('Payroll/Payslips');
    })->name('admin.payroll.payslips');

    Route::get('/departments', function () {
        return Inertia::render('Departments/Manage');
    })->name('admin.departments.manage');

    Route::get('/work-schedules', function () {
        return Inertia::render('WorkSchedules/Index');
    })->name('admin.work-schedules.index');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');

    // Employee API routes
    Route::prefix('api')->group(function () {
        Route::get('/employees', [\App\Http\Controllers\Api\EmployeeController::class, 'index'])->name('api.employees.index');
        Route::post('/employees', [\App\Http\Controllers\Api\EmployeeController::class, 'store'])->name('api.employees.store');
        Route::get('/employees/{id}', [\App\Http\Controllers\Api\EmployeeController::class, 'show'])->name('api.employees.show');
        Route::put('/employees/{id}', [\App\Http\Controllers\Api\EmployeeController::class, 'update'])->name('api.employees.update');
    });

    Route::get('/settings/holidays', function () {
        return Inertia::render('Settings/Holidays');
    })->name('admin.settings.holidays');

    Route::get('/settings/schedule-overrides', function () {
        return Inertia::render('Settings/ScheduleOverrides');
    })->name('admin.settings.schedule-overrides');

    Route::get('/settings/color-palette', function () {
        return Inertia::render('Settings/ColorPalette');
    })->name('admin.settings.color-palette');
});

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
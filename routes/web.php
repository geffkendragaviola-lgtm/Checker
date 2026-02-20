<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Models\Department;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    $departments = Department::query()
        ->orderBy('name')
        ->get();

    return view('dashboard', compact('departments'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/upload', [AttendanceController::class, 'upload'])->name('attendance.upload');
});

Route::middleware(['auth'])->group(function () {
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
});


require __DIR__.'/auth.php';

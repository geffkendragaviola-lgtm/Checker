<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\Payroll;
use App\Models\Holiday;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function attendance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $query = AttendanceRecord::with('employee.department')
            ->whereBetween('attendance_date', [$request->start_date, $request->end_date]);

        if ($request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $records = $query->orderBy('attendance_date')->get();

        // Calculate summary
        $summary = [
            'total_records' => $records->count(),
            'total_late' => $records->where('total_late_minutes', '>', 0)->count(),
            'total_absent' => $records->where('workday_rendered', 0)->count(),
            'total_late_minutes' => $records->sum('total_late_minutes'),
            'total_undertime' => $records->sum('undertime_minutes'),
        ];

        // Group by employee
        $byEmployee = $records->groupBy('employee_id')->map(function($empRecords) {
            return [
                'name' => $empRecords->first()->employee->full_name,
                'department' => $empRecords->first()->employee->department->name,
                'days_present' => $empRecords->where('workday_rendered', '>', 0)->count(),
                'days_absent' => $empRecords->where('workday_rendered', 0)->count(),
                'late_incidents' => $empRecords->where('total_late_minutes', '>', 0)->count(),
                'total_late_minutes' => $empRecords->sum('total_late_minutes'),
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'summary' => $summary,
                'by_employee' => $byEmployee,
                'records' => $records,
            ]);
        }

        return view('reports.attendance', compact('records', 'summary', 'byEmployee'));
    }

    public function payroll(Request $request)
    {
        $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $query = Payroll::with(['employee.department', 'payrollItems'])
            ->where('payroll_period_id', $request->payroll_period_id);

        if ($request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $payrolls = $query->get();

        $summary = [
            'total_gross' => $payrolls->sum('gross_pay'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'total_net' => $payrolls->sum('net_pay'),
            'total_employees' => $payrolls->count(),
            'total_late_deductions' => $payrolls->sum('late_deductions'),
            'total_absent_deductions' => $payrolls->sum('absent_deductions'),
            'total_holiday_pay' => $payrolls->sum('holiday_pay'),
        ];

        return view('reports.payroll', compact('payrolls', 'summary'));
    }

    public function holidays(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $holidays = Holiday::whereYear('holiday_date', $request->year)
            ->orderBy('holiday_date')
            ->get();

        $summary = [
            'total' => $holidays->count(),
            'regular' => $holidays->where('type', 'regular')->count(),
            'special' => $holidays->where('type', 'special')->count(),
            'local' => $holidays->where('type', 'local')->count(),
            'paid' => $holidays->where('is_paid', true)->count(),
        ];

        // Group by month
        $byMonth = $holidays->groupBy(function($holiday) {
            return Carbon::parse($holiday->holiday_date)->format('F');
        });

        return view('reports.holidays', compact('holidays', 'summary', 'byMonth'));
    }
}
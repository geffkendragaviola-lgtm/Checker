<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\AttendanceRecord;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\PayrollItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();

        // Basic Stats with safe defaults
        $stats = [
            'total_employees' => Employee::where('employment_status', 'active')->count() ?: 0,
            'present_today' => AttendanceRecord::where('attendance_date', Carbon::today())
                ->where('workday_rendered', '>', 0)
                ->count() ?: 0,
            'late_today' => AttendanceRecord::where('attendance_date', Carbon::today())
                ->where('total_late_minutes', '>', 0)
                ->count() ?: 0,
            'on_leave_today' => Employee::where('employment_status', 'on_leave')->count() ?: 0,
        ];

        // Upcoming Holidays
        $upcomingHolidays = Holiday::where('holiday_date', '>=', Carbon::today())
            ->orderBy('holiday_date')
            ->limit(5)
            ->get()
            ->map(function($holiday) {
                return [
                    'name' => $holiday->name ?? 'Unknown',
                    'date' => $holiday->holiday_date ? $holiday->holiday_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'type' => $holiday->type ?? 'regular',
                    'rate' => $holiday->rate_multiplier ?? 1.0,
                ];
            });

        // Holiday Stats
        $currentYear = Carbon::now()->year;
        $holidayStats = [
            'total' => Holiday::whereYear('holiday_date', $currentYear)->count() ?: 0,
            'regular' => Holiday::whereYear('holiday_date', $currentYear)
                ->where('type', 'regular')->count() ?: 0,
            'special' => Holiday::whereYear('holiday_date', $currentYear)
                ->where('type', 'special')->count() ?: 0,
        ];

        // Current Payroll Period
        $currentPayrollPeriod = PayrollPeriod::where('status', 'open')->first();
        $payrollStats = [
            'total_payroll' => 0,
            'employees_processed' => 0,
            'pending' => 0,
            'late_deductions' => 0,
            'absence_deductions' => 0,
            'holiday_pay' => 0,
        ];

        $currentPayrollData = null;
        if ($currentPayrollPeriod) {
            $totalEmployees = Employee::where('employment_status', 'active')->count() ?: 1;
            $processedCount = Payroll::where('payroll_period_id', $currentPayrollPeriod->id)->count();
            
            $currentPayrollData = [
                'name' => $currentPayrollPeriod->name ?? 'Current Period',
                'start_date' => $currentPayrollPeriod->start_date ? Carbon::parse($currentPayrollPeriod->start_date) : Carbon::now(),
                'end_date' => $currentPayrollPeriod->end_date ? Carbon::parse($currentPayrollPeriod->end_date) : Carbon::now(),
                'progress' => $totalEmployees > 0 ? round(($processedCount / $totalEmployees) * 100) : 0,
            ];
            
            $payrollStats = [
                'total_payroll' => Payroll::where('payroll_period_id', $currentPayrollPeriod->id)->sum('net_pay') ?: 0,
                'employees_processed' => $processedCount,
                'pending' => max(0, $totalEmployees - $processedCount),
                'late_deductions' => PayrollItem::whereHas('payroll', function($q) use ($currentPayrollPeriod) {
                    $q->where('payroll_period_id', $currentPayrollPeriod->id);
                })->where('category', 'late')->sum('amount') ?: 0,
                'absence_deductions' => PayrollItem::whereHas('payroll', function($q) use ($currentPayrollPeriod) {
                    $q->where('payroll_period_id', $currentPayrollPeriod->id);
                })->where('category', 'absence')->sum('amount') ?: 0,
                'holiday_pay' => PayrollItem::whereHas('payroll', function($q) use ($currentPayrollPeriod) {
                    $q->where('payroll_period_id', $currentPayrollPeriod->id);
                })->where('category', 'holiday')->sum('amount') ?: 0,
            ];
        }

        // Department Statistics
        $departmentsWithCount = Department::withCount(['employees' => function($q) {
            $q->where('employment_status', 'active');
        }])->get();

        $totalEmployees = $departmentsWithCount->sum('employees_count') ?: 1;
        
        $departmentStats = $departmentsWithCount->map(function($dept) use ($totalEmployees) {
            return [
                'name' => $dept->name ?? 'Unknown',
                'count' => $dept->employees_count ?? 0,
                'percentage' => $totalEmployees > 0 ? round((($dept->employees_count ?? 0) / $totalEmployees) * 100) : 0,
            ];
        });

        // Department Chart Data
        $departmentChart = [
            'labels' => $departmentStats->pluck('name')->toArray() ?: ['No Departments'],
            'data' => $departmentStats->pluck('count')->toArray() ?: [1],
        ];

        // Attendance Chart Data (Last 7 days)
        $attendanceChart = $this->getAttendanceChartData(7);

        // Recent Activities
        $recentActivities = $this->getRecentActivities();

        // System Status
        $systemStatus = [
            'last_backup' => 'Today, 2:30 AM',
            'storage_used' => '2.4 GB / 10 GB',
            'active_users' => 1,
        ];

        return view('dashboard', compact(
            'departments',
            'stats',
            'upcomingHolidays',
            'holidayStats',
            'currentPayrollData',
            'payrollStats',
            'departmentStats',
            'departmentChart',
            'attendanceChart',
            'recentActivities',
            'systemStatus'
        ));
    }

    private function getAttendanceChartData($days)
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days - 1);

        $dates = [];
        $present = [];
        $late = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dates[] = $date->format('D');
            
            $attendance = AttendanceRecord::where('attendance_date', $date->format('Y-m-d'))
                ->select(
                    DB::raw('COALESCE(COUNT(*), 0) as total'),
                    DB::raw('COALESCE(SUM(CASE WHEN total_late_minutes > 0 THEN 1 ELSE 0 END), 0) as late_count')
                )
                ->first();

            $total = $attendance ? ($attendance->total ?? 0) : 0;
            $lateCount = $attendance ? ($attendance->late_count ?? 0) : 0;
            
            $present[] = max(0, $total - $lateCount);
            $late[] = $lateCount;
        }

        return [
            'labels' => $dates,
            'present' => $present,
            'late' => $late,
        ];
    }

    private function getRecentActivities()
    {
        $activities = [];

        // Recent attendance uploads
        try {
            $recentAttendance = AttendanceRecord::with('employee')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentAttendance as $att) {
                if ($att->employee) {
                    $activities[] = [
                        'type' => 'attendance',
                        'title' => 'Attendance Recorded',
                        'description' => ($att->employee->first_name ?? 'Unknown') . ' ' . ($att->employee->last_name ?? '') . ' - ' . 
                            ($att->attendance_date ? $att->attendance_date->format('M d, Y') : 'Unknown date'),
                        'time' => $att->created_at ? $att->created_at->diffForHumans() : 'Unknown',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        // Recent holidays
        try {
            $recentHolidays = Holiday::orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($recentHolidays as $holiday) {
                $activities[] = [
                    'type' => 'holiday',
                    'title' => 'Holiday Added',
                    'description' => ($holiday->name ?? 'Unknown') . ' (' . ($holiday->holiday_date ? $holiday->holiday_date->format('M d, Y') : 'Unknown') . ')',
                    'time' => $holiday->created_at ? $holiday->created_at->diffForHumans() : 'Unknown',
                ];
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        // Recent payroll
        try {
            $recentPayroll = Payroll::with('employee')
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($recentPayroll as $pay) {
                if ($pay->employee) {
                    $activities[] = [
                        'type' => 'payroll',
                        'title' => 'Payroll Processed',
                        'description' => ($pay->employee->first_name ?? 'Unknown') . ' ' . ($pay->employee->last_name ?? '') . ' - ₱' . 
                            number_format($pay->net_pay ?? 0, 2),
                        'time' => $pay->created_at ? $pay->created_at->diffForHumans() : 'Unknown',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time'] ?? 'now') - strtotime($a['time'] ?? 'now');
        });

        return array_slice($activities, 0, 10);
    }

    public function getAttendanceData(Request $request)
    {
        $days = $request->get('days', 7);
        $data = $this->getAttendanceChartData($days);

        return response()->json($data);
    }
}
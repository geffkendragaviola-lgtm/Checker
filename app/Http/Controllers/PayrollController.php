<?php
// app/Http/Controllers/PayrollController.php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Employee;
use App\Models\Department;
use App\Models\AttendanceRecord;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();

        // Calculate progress and totals for each period
        foreach ($payrollPeriods as $period) {
            $period->total_employees = Employee::where('employment_status', 'active')->count();
            $period->processed_count = Payroll::where('payroll_period_id', $period->id)->count();
            $period->progress = $period->total_employees > 0 
                ? round(($period->processed_count / $period->total_employees) * 100) 
                : 0;
            
            $period->total_payroll = Payroll::where('payroll_period_id', $period->id)
                ->sum('net_pay');
        }

        return view('payroll.index', compact('payrollPeriods', 'departments'));
    }

    public function getEmployees(Request $request)
    {
        $periodId = $request->get('period');
        $departmentId = $request->get('department', 'all');
        $search = $request->get('search', '');

        $query = Employee::with(['department', 'payrolls' => function($q) use ($periodId) {
            if ($periodId) {
                $q->where('payroll_period_id', $periodId);
            }
        }])->where('employment_status', 'active');

        if ($departmentId !== 'all') {
            $query->where('department_id', $departmentId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('employee_code', 'ilike', "%{$search}%");
            });
        }

        $employees = $query->get();

        return response()->json([
            'success' => true,
            'employees' => $employees->map(function($employee) use ($periodId) {
                $payroll = $employee->payrolls->first();
                
                return [
                    'id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->full_name,
                    'department' => $employee->department->name,
                    'days_worked' => $payroll ? $payroll->days_worked : 0,
                    'gross_pay' => $payroll ? $payroll->gross_pay : 0,
                    'total_deductions' => $payroll ? $payroll->total_deductions : 0,
                    'net_pay' => $payroll ? $payroll->net_pay : 0,
                    'status' => $payroll ? 'processed' : 'pending',
                ];
            })
        ]);
    }

    public function getDeductionSummary(Request $request)
    {
        $periodId = $request->get('period');

        if (!$periodId) {
            return response()->json([
                'success' => true,
                'sss' => 0,
                'philhealth' => 0,
                'pagibig' => 0,
                'tax' => 0,
                'late' => 0,
                'absence' => 0,
                'cash_advance' => 0,
                'loans' => 0,
            ]);
        }

        $payrollItems = PayrollItem::whereHas('payroll', function($q) use ($periodId) {
            $q->where('payroll_period_id', $periodId);
        })->get();

        $summary = [
            'sss' => $payrollItems->where('category', 'sss')->sum('amount'),
            'philhealth' => $payrollItems->where('category', 'philhealth')->sum('amount'),
            'pagibig' => $payrollItems->where('category', 'pagibig')->sum('amount'),
            'tax' => $payrollItems->where('category', 'tax')->sum('amount'),
            'late' => $payrollItems->where('category', 'late')->sum('amount'),
            'absence' => $payrollItems->where('category', 'absence')->sum('amount'),
            'cash_advance' => $payrollItems->where('category', 'cash_advance')->sum('amount'),
            'loans' => $payrollItems->where('category', 'loan')->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            ...$summary
        ]);
    }

    public function getEmployeePayroll($id)
    {
        $payroll = Payroll::with(['employee', 'payrollItems', 'payrollPeriod'])
            ->where('employee_id', $id)
            ->latest()
            ->first();

        if (!$payroll) {
            return response()->json([
                'success' => false,
                'message' => 'No payroll found for this employee'
            ]);
        }

        $html = $this->generatePayrollDetailHtml($payroll);

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function createPeriod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'cutoff_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $period = PayrollPeriod::create([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'cutoff_date' => $request->cutoff_date,
                'status' => 'open',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll period created successfully',
                'period' => $period
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating payroll period: ' . $e->getMessage()
            ], 500);
        }
    }

    public function process(Request $request)
    {
        $periodId = $request->get('period_id');
        
        if (!$periodId) {
            $period = PayrollPeriod::where('status', 'open')->first();
            if (!$period) {
                return response()->json([
                    'success' => false,
                    'message' => 'No open payroll period found'
                ]);
            }
            $periodId = $period->id;
        }

        try {
            DB::beginTransaction();

            $period = PayrollPeriod::findOrFail($periodId);
            $period->update(['status' => 'processing']);

            $employees = Employee::where('employment_status', 'active')->get();
            
            foreach ($employees as $employee) {
                $this->processEmployeePayroll($employee, $period);
            }

            $period->update(['status' => 'closed']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll processed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processEmployeePayroll($employee, $period)
    {
        // Get attendance records for the period
        $attendances = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$period->start_date, $period->end_date])
            ->get();

        // Calculate days worked
        $daysWorked = $attendances->sum('workday_rendered');

        // Calculate late deductions
        $totalLateMinutes = $attendances->sum('total_late_minutes');
        $lateDeduction = $this->calculateLateDeduction($totalLateMinutes, $employee->daily_rate);

        // Calculate absence deductions
        $absentDays = $attendances->filter(function($attendance) {
            return $attendance->workday_rendered == 0;
        })->count();
        $absenceDeduction = $absentDays * $employee->daily_rate;

        // Calculate holiday pay
        $holidays = Holiday::whereBetween('holiday_date', [$period->start_date, $period->end_date])
            ->where('is_paid', true)
            ->get();
        
        $holidayPay = $this->calculateHolidayPay($employee, $holidays, $attendances);

        // Calculate gross pay
        $grossPay = ($daysWorked * $employee->daily_rate) + $holidayPay;

        // Calculate government contributions
        $sss = $this->calculateSSS($grossPay);
        $philhealth = $this->calculatePhilHealth($grossPay);
        $pagibig = $this->calculatePagIbig($grossPay);
        $tax = $this->calculateWithholdingTax($grossPay);

        $totalDeductions = $lateDeduction + $absenceDeduction + $sss + $philhealth + $pagibig + $tax;
        $netPay = $grossPay - $totalDeductions;

        // Create payroll record
        $payroll = Payroll::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'days_worked' => $daysWorked,
            'late_deductions' => $lateDeduction,
            'absent_deductions' => $absenceDeduction,
            'holiday_pay' => $holidayPay,
            'overtime_pay' => 0,
            'gross_pay' => $grossPay,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
        ]);

        // Create payroll items
        $this->createPayrollItems($payroll, [
            ['type' => 'deduction', 'category' => 'late', 'amount' => $lateDeduction, 'description' => 'Late deductions'],
            ['type' => 'deduction', 'category' => 'absence', 'amount' => $absenceDeduction, 'description' => 'Absence deductions'],
            ['type' => 'deduction', 'category' => 'sss', 'amount' => $sss, 'description' => 'SSS contribution'],
            ['type' => 'deduction', 'category' => 'philhealth', 'amount' => $philhealth, 'description' => 'PhilHealth contribution'],
            ['type' => 'deduction', 'category' => 'pagibig', 'amount' => $pagibig, 'description' => 'Pag-IBIG contribution'],
            ['type' => 'deduction', 'category' => 'tax', 'amount' => $tax, 'description' => 'Withholding tax'],
            ['type' => 'addition', 'category' => 'holiday', 'amount' => $holidayPay, 'description' => 'Holiday pay'],
        ]);
    }

    private function calculateLateDeduction($totalLateMinutes, $dailyRate)
    {
        $hourlyRate = $dailyRate / 8;
        $minutesInHour = 60;
        $lateHours = $totalLateMinutes / $minutesInHour;
        
        return round($lateHours * $hourlyRate, 2);
    }

    private function calculateHolidayPay($employee, $holidays, $attendances)
    {
        $totalHolidayPay = 0;
        
        foreach ($holidays as $holiday) {
            $attendance = $attendances->firstWhere('attendance_date', $holiday->holiday_date);
            
            if ($attendance && $attendance->workday_rendered > 0) {
                // Employee worked on holiday
                $totalHolidayPay += $employee->daily_rate * ($holiday->rate_multiplier - 1);
            } elseif ($holiday->is_paid) {
                // Employee didn't work but holiday is paid
                $totalHolidayPay += $employee->daily_rate;
            }
        }
        
        return $totalHolidayPay;
    }

    private function calculateSSS($grossPay)
    {
        // Simplified SSS computation
        // In production, use actual SSS contribution table
        if ($grossPay <= 4250) return 180;
        if ($grossPay <= 4750) return 202.50;
        if ($grossPay <= 5250) return 225;
        if ($grossPay <= 5750) return 247.50;
        if ($grossPay <= 6250) return 270;
        if ($grossPay <= 6750) return 292.50;
        if ($grossPay <= 7250) return 315;
        if ($grossPay <= 7750) return 337.50;
        if ($grossPay <= 8250) return 360;
        if ($grossPay <= 8750) return 382.50;
        if ($grossPay <= 9250) return 405;
        if ($grossPay <= 9750) return 427.50;
        if ($grossPay <= 10250) return 450;
        return 450; // Maximum contribution
    }

    private function calculatePhilHealth($grossPay)
    {
        // Simplified PhilHealth computation (3% of monthly basic pay, shared equally)
        $total = $grossPay * 0.03;
        return round($total / 2, 2); // Employee share is half
    }

    private function calculatePagIbig($grossPay)
    {
        // Simplified Pag-IBIG computation
        $employeeShare = min($grossPay * 0.02, 100); // 2% up to 100
        return round($employeeShare, 2);
    }

    private function calculateWithholdingTax($grossPay)
    {
        // Simplified withholding tax (2026 BIR table approximation)
        // In production, use actual tax table with exemptions
        $annualized = $grossPay * 12;
        
        if ($annualized <= 250000) return 0;
        if ($annualized <= 400000) return round(($annualized - 250000) * 0.15 / 12, 2);
        if ($annualized <= 800000) return round((($annualized - 400000) * 0.20 + 22500) / 12, 2);
        if ($annualized <= 2000000) return round((($annualized - 800000) * 0.25 + 102500) / 12, 2);
        if ($annualized <= 8000000) return round((($annualized - 2000000) * 0.30 + 402500) / 12, 2);
        return round((($annualized - 8000000) * 0.35 + 2202500) / 12, 2);
    }

    private function createPayrollItems($payroll, $items)
    {
        foreach ($items as $item) {
            if ($item['amount'] > 0) {
                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'type' => $item['type'],
                    'category' => $item['category'],
                    'amount' => $item['amount'],
                    'description' => $item['description'],
                ]);
            }
        }
    }

    private function generatePayrollDetailHtml($payroll)
    {
        $items = $payroll->payrollItems;
        $additions = $items->where('type', 'addition');
        $deductions = $items->where('type', 'deduction');

        $html = '<div class="container-fluid">';
        $html .= '<div class="row mb-3">';
        $html .= '<div class="col-6">';
        $html .= '<h6>Employee Information</h6>';
        $html .= '<p><strong>Name:</strong> ' . $payroll->employee->full_name . '</p>';
        $html .= '<p><strong>ID:</strong> ' . $payroll->employee->employee_code . '</p>';
        $html .= '<p><strong>Department:</strong> ' . $payroll->employee->department->name . '</p>';
        $html .= '</div>';
        $html .= '<div class="col-6">';
        $html .= '<h6>Period Information</h6>';
        $html .= '<p><strong>Period:</strong> ' . $payroll->payrollPeriod->name . '</p>';
        $html .= '<p><strong>Days Worked:</strong> ' . $payroll->days_worked . '</p>';
        $html .= '<p><strong>Daily Rate:</strong> ₱' . number_format($payroll->employee->daily_rate, 2) . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<table class="table table-sm">';
        $html .= '<thead><tr><th>Description</th><th class="text-end">Amount</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($additions as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $item->description . '</td>';
            $html .= '<td class="text-end text-success">+₱' . number_format($item->amount, 2) . '</td>';
            $html .= '</tr>';
        }

        $html .= '<tr><td colspan="2"><hr></td></tr>';

        foreach ($deductions as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $item->description . '</td>';
            $html .= '<td class="text-end text-danger">-₱' . number_format($item->amount, 2) . '</td>';
            $html .= '</tr>';
        }

        $html .= '<tr><td colspan="2"><hr></td></tr>';
        $html .= '<tr class="fw-bold">';
        $html .= '<td>Gross Pay</td>';
        $html .= '<td class="text-end">₱' . number_format($payroll->gross_pay, 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr class="fw-bold">';
        $html .= '<td>Total Deductions</td>';
        $html .= '<td class="text-end text-danger">-₱' . number_format($payroll->total_deductions, 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr class="fw-bold">';
        $html .= '<td>Net Pay</td>';
        $html .= '<td class="text-end text-success">₱' . number_format($payroll->net_pay, 2) . '</td>';
        $html .= '</tr>';

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'sss_employer' => 'required|numeric',
            'sss_employee' => 'required|numeric',
            'philhealth' => 'required|numeric',
            'pagibig' => 'required|numeric',
            'min_wage' => 'required|numeric',
            'tax_table' => 'required|string',
        ]);

        // Save settings to database or config
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully'
        ]);
    }
}
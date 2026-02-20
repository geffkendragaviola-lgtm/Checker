<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        return view('attendance.index', compact('departments'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'logs' => 'required|array',
            'processed' => 'required|array'
        ]);

        DB::beginTransaction();
        
        try {
            // Save raw logs
            foreach ($request->logs as $log) {
                AttendanceLog::updateOrCreate(
                    [
                        'employee_code' => $log['employeeId'],
                        'log_datetime' => $log['date'] . ' ' . $log['time'],
                        'log_type' => $log['activity']
                    ],
                    [
                        'source_file' => $log['remarks'] ?? 'upload',
                        'is_processed' => true
                    ]
                );
            }

            // Save processed attendance records
            foreach ($request->processed as $record) {
                $employee = Employee::where('employee_code', $record['employeeId'])->first();
                
                if ($employee) {
                    AttendanceRecord::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'attendance_date' => $record['date']
                        ],
                        [
                            'schedule_id' => $this->getScheduleIdForDepartment($record['department']),
                            'time_in_am' => $record['rawTimeIn'] ?? null,
                            'time_out_lunch' => $record['rawBreakOut'] ?? null,
                            'time_in_pm' => $record['rawBreakIn'] ?? null,
                            'time_out_pm' => $record['rawTimeOut'] ?? null,
                            'late_minutes_am' => $record['lateMinutes'] ?? 0,
                            'late_minutes_pm' => $record['lateBreakInMinutes'] ?? 0,
                            'total_late_minutes' => $record['totalLateMinutesOverall'] ?? 0,
                            'workday_rendered' => $this->calculateWorkdayRendered($record),
                            'missing_logs' => $this->hasMissingLogs($record),
                            'remarks' => $record['status'] ?? null
                        ]
                    );
                }
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance data saved successfully',
                'logs_count' => count($request->logs),
                'records_count' => count($request->processed)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getScheduleIdForDepartment($departmentName)
    {
        $department = Department::where('name', $departmentName)->first();
        return $department ? $department->schedule_id : 1;
    }

    private function calculateWorkdayRendered($record)
    {
        if (isset($record['isWholeDayAbsent']) && $record['isWholeDayAbsent']) {
            return 0;
        }
        
        $status = $record['status'] ?? '';
        if (strpos($status, 'Half Day') !== false || 
            strpos($status, 'Absent AM') !== false || 
            strpos($status, 'Absent PM') !== false) {
            return 0.5;
        }
        
        return 1.0;
    }

    private function hasMissingLogs($record)
    {
        $logs = [
            $record['rawTimeIn'] ?? null,
            $record['rawBreakOut'] ?? null,
            $record['rawBreakIn'] ?? null,
            $record['rawTimeOut'] ?? null
        ];
        
        return count(array_filter($logs)) < 4;
    }
}
<?php
// app/Http/Controllers/HolidayController.php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Department;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        $schedules = WorkSchedule::orderBy('name')->get();
        
        return view('holidays.index', compact('departments', 'schedules'));
    }

    public function getData(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $type = $request->get('type', 'all');
        $departmentId = $request->get('department', 'all');

        $query = Holiday::whereYear('holiday_date', $year);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($departmentId !== 'all') {
            $query->where(function($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->orWhereNull('department_id');
            });
        }

        $holidays = $query->with(['department', 'workSchedule'])->get();

        return response()->json([
            'success' => true,
            'holidays' => $holidays->map(function($holiday) {
                return [
                    'id' => $holiday->id,
                    'name' => $holiday->name,
                    'holiday_date' => $holiday->holiday_date->format('Y-m-d'),
                    'type' => $holiday->type,
                    'rate_multiplier' => $holiday->rate_multiplier,
                    'is_paid' => $holiday->is_paid,
                    'department_id' => $holiday->department_id,
                    'department_name' => $holiday->department ? $holiday->department->name : null,
                    'schedule_id' => $holiday->schedule_id,
                    'schedule_name' => $holiday->workSchedule ? $holiday->workSchedule->name : null,
                ];
            })
        ]);
    }

    public function getStats()
    {
        $currentYear = Carbon::now()->year;
        $holidays = Holiday::whereYear('holiday_date', $currentYear)->get();

        $stats = [
            'total' => $holidays->count(),
            'regular' => $holidays->where('type', 'regular')->count(),
            'special' => $holidays->where('type', 'special')->count(),
            'local' => $holidays->where('type', 'local')->count(),
            'upcoming' => $holidays->where('holiday_date', '>=', Carbon::now())->count(),
            'paid' => $holidays->where('is_paid', true)->count(),
            'q1' => $holidays->whereBetween('holiday_date', [
                Carbon::create($currentYear, 1, 1),
                Carbon::create($currentYear, 3, 31)
            ])->count(),
            'q2' => $holidays->whereBetween('holiday_date', [
                Carbon::create($currentYear, 4, 1),
                Carbon::create($currentYear, 6, 30)
            ])->count(),
            'q3' => $holidays->whereBetween('holiday_date', [
                Carbon::create($currentYear, 7, 1),
                Carbon::create($currentYear, 9, 30)
            ])->count(),
            'q4' => $holidays->whereBetween('holiday_date', [
                Carbon::create($currentYear, 10, 1),
                Carbon::create($currentYear, 12, 31)
            ])->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function show($id)
    {
        $holiday = Holiday::with(['department', 'workSchedule'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'holiday' => [
                'id' => $holiday->id,
                'name' => $holiday->name,
                'holiday_date' => $holiday->holiday_date->format('Y-m-d'),
                'type' => $holiday->type,
                'rate_multiplier' => $holiday->rate_multiplier,
                'is_paid' => $holiday->is_paid,
                'department_id' => $holiday->department_id,
                'schedule_id' => $holiday->schedule_id,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'type' => 'required|in:regular,special,local',
            'rate_multiplier' => 'required|numeric|min:1|max:3',
            'is_paid' => 'boolean',
            'department_id' => 'nullable|exists:departments,id',
            'schedule_id' => 'nullable|exists:work_schedules,id',
        ]);

        try {
            $holiday = Holiday::create([
                'name' => $request->name,
                'holiday_date' => $request->holiday_date,
                'type' => $request->type,
                'rate_multiplier' => $request->rate_multiplier,
                'is_paid' => $request->has('is_paid'),
                'department_id' => $request->department_id,
                'schedule_id' => $request->schedule_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Holiday created successfully',
                'holiday' => $holiday
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating holiday: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'type' => 'required|in:regular,special,local',
            'rate_multiplier' => 'required|numeric|min:1|max:3',
            'is_paid' => 'boolean',
            'department_id' => 'nullable|exists:departments,id',
            'schedule_id' => 'nullable|exists:work_schedules,id',
        ]);

        try {
            $holiday = Holiday::findOrFail($id);
            
            $holiday->update([
                'name' => $request->name,
                'holiday_date' => $request->holiday_date,
                'type' => $request->type,
                'rate_multiplier' => $request->rate_multiplier,
                'is_paid' => $request->has('is_paid'),
                'department_id' => $request->department_id,
                'schedule_id' => $request->schedule_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Holiday updated successfully',
                'holiday' => $holiday
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating holiday: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $holiday = Holiday::findOrFail($id);
            $holiday->delete();

            return response()->json([
                'success' => true,
                'message' => 'Holiday deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting holiday: ' . $e->getMessage()
            ], 500);
        }
    }
}
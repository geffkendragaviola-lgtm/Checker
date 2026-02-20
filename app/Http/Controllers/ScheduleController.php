<?php
// app/Http/Controllers/ScheduleController.php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = WorkSchedule::orderBy('name')->get();
        $departments = Department::with('workSchedule')->get();
        
        return view('schedules.index', compact('schedules', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:work_schedules',
            'work_start_time' => 'required',
            'work_end_time' => 'required|after:work_start_time',
            'break_start_time' => 'nullable',
            'break_end_time' => 'nullable|after:break_start_time',
            'grace_period_minutes' => 'required|integer|min:0',
            'is_working_day' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            WorkSchedule::create($request->all());

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'Schedule created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating schedule: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $schedule = WorkSchedule::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:work_schedules,name,' . $id,
            'work_start_time' => 'required',
            'work_end_time' => 'required|after:work_start_time',
            'break_start_time' => 'nullable',
            'break_end_time' => 'nullable|after:break_start_time',
            'grace_period_minutes' => 'required|integer|min:0',
            'is_working_day' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $schedule->update($request->all());

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'Schedule updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating schedule: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = WorkSchedule::findOrFail($id);
            
            // Check if schedule is being used by departments
            if ($schedule->departments()->count() > 0) {
                return back()->with('error', 'Cannot delete schedule because it is assigned to departments.');
            }

            $schedule->delete();

            return redirect()->route('schedules.index')
                ->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting schedule: ' . $e->getMessage());
        }
    }
}
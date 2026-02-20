<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')
            ->orderBy('last_name')
            ->paginate(15);
            
        $departments = Department::orderBy('name')->get();
        
        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees',
            'first_name' => 'required',
            'last_name' => 'required',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required',
            'daily_rate' => 'required|numeric',
            'hire_date' => 'required|date',
            'email' => 'nullable|email|unique:employees',
        ]);

        try {
            DB::beginTransaction();

            Employee::create($request->all());

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating employee: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $employee = Employee::with(['department', 'attendanceRecords', 'payrolls'])
            ->findOrFail($id);
            
        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,' . $id,
            'first_name' => 'required',
            'last_name' => 'required',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required',
            'daily_rate' => 'required|numeric',
            'hire_date' => 'required|date',
            'email' => 'nullable|email|unique:employees,email,' . $id,
        ]);

        try {
            DB::beginTransaction();

            $employee->update($request->all());

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return redirect()->route('employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }
}
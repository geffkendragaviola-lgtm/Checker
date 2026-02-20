{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Employees</h1>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Employee
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Daily Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->department->name }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>₱{{ number_format($employee->daily_rate, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $employee->employment_status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($employee->employment_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
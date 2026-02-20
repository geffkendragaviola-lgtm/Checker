{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Reports</h1>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history text-primary display-4"></i>
                    <h5 class="mt-2">Attendance Report</h5>
                    <a href="{{ route('reports.attendance') }}" class="btn btn-sm btn-outline-primary mt-2">Generate</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack text-success display-4"></i>
                    <h5 class="mt-2">Payroll Report</h5>
                    <a href="{{ route('reports.payroll') }}" class="btn btn-sm btn-outline-primary mt-2">Generate</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event text-warning display-4"></i>
                    <h5 class="mt-2">Holiday Report</h5>
                    <a href="{{ route('reports.holidays') }}" class="btn btn-sm btn-outline-primary mt-2">Generate</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
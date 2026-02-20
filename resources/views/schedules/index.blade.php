{{-- resources/views/schedules/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Work Schedules')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Work Schedules</h1>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Schedule
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Work schedules management module - Under Construction</p>
        </div>
    </div>
</div>
@endsection
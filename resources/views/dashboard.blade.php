{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<style>
    .dashboard-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        position: relative;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .dashboard-card .icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #4e73df;
    }
    .dashboard-card .title {
        font-size: 1rem;
        color: #858796;
        margin-bottom: 5px;
    }
    .dashboard-card .value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 10px;
    }
    .dashboard-card .link {
        color: #4e73df;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .dashboard-card .link:hover {
        text-decoration: underline;
    }
    .dashboard-card.primary { border-left: 4px solid #4e73df; }
    .dashboard-card.success { border-left: 4px solid #1cc88a; }
    .dashboard-card.info { border-left: 4px solid #36b9cc; }
    .dashboard-card.warning { border-left: 4px solid #f6c23e; }
    .dashboard-card.danger { border-left: 4px solid #e74a3b; }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stat-card:nth-child(2) { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card:nth-child(3) { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card:nth-child(4) { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
    }

    .quick-action {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f8f9fc;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .quick-action:hover {
        background: #e9ecef;
        text-decoration: none;
        color: inherit;
    }
    .quick-action i {
        font-size: 1.5rem;
        width: 40px;
        color: #4e73df;
    }
    .quick-action .content {
        flex: 1;
    }
    .quick-action .title {
        font-weight: 600;
        margin-bottom: 2px;
    }
    .quick-action .subtitle {
        font-size: 0.85rem;
        color: #858796;
    }

    .recent-item {
        padding: 12px 15px;
        border-bottom: 1px solid #e3e6f0;
        transition: background 0.2s;
    }
    .recent-item:hover {
        background: #f8f9fc;
    }
    .recent-item:last-child {
        border-bottom: none;
    }

    .badge-holiday {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-holiday.regular { background: #e74a3b; color: white; }
    .badge-holiday.special { background: #f6c23e; color: white; }
    .badge-holiday.local { background: #36b9cc; color: white; }

    .progress-sm {
        height: 8px;
        border-radius: 4px;
    }

    .weather-widget {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-grid me-2"></i>Dashboard
                    </h1>
                    <p class="text-muted mt-2 mb-0">
                        Welcome back, {{ Auth::user()->name }}! Here's what's happening with your HR system today.
                    </p>
                </div>
                <div class="text-end">
                    <div class="text-muted">{{ now()->format('l, F j, Y') }}</div>
                    <div class="h5 mt-1" id="liveClock"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card primary">
                <div class="icon"><i class="bi bi-people"></i></div>
                <div class="title">Total Employees</div>
                <div class="value">{{ $stats['total_employees'] ?? 0 }}</div>
                <a href="{{ route('employees.index') }}" class="link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card success">
                <div class="icon"><i class="bi bi-check-circle"></i></div>
                <div class="title">Present Today</div>
                <div class="value">{{ $stats['present_today'] ?? 0 }}</div>
                <a href="{{ route('attendance.index') }}" class="link">View attendance <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card warning">
                <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="title">Late Today</div>
                <div class="value">{{ $stats['late_today'] ?? 0 }}</div>
                <a href="{{ route('attendance.index') }}" class="link">View late <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card info">
                <div class="icon"><i class="bi bi-calendar-check"></i></div>
                <div class="title">On Leave</div>
                <div class="value">{{ $stats['on_leave_today'] ?? 0 }}</div>
                <a href="#" class="link">View leaves <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row">
        <!-- Left Column - Main Features -->
        <div class="col-lg-8">
            <!-- Quick Actions Cards -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{{ route('attendance.index') }}" class="quick-action">
                                        <i class="bi bi-clock-history"></i>
                                        <div class="content">
                                            <div class="title">Attendance Checker</div>
                                            <div class="subtitle">Upload & monitor attendance</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('holidays.index') }}" class="quick-action">
                                        <i class="bi bi-calendar-event"></i>
                                        <div class="content">
                                            <div class="title">Holiday Calendar</div>
                                            <div class="subtitle">Manage holidays & rates</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('payroll.index') }}" class="quick-action">
                                        <i class="bi bi-cash-stack"></i>
                                        <div class="content">
                                            <div class="title">Payroll</div>
                                            <div class="subtitle">Process payroll & deductions</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('employees.index') }}" class="quick-action">
                                        <i class="bi bi-person-badge"></i>
                                        <div class="content">
                                            <div class="title">Employees</div>
                                            <div class="subtitle">Manage employee records</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('schedules.index') }}" class="quick-action">
                                        <i class="bi bi-clock"></i>
                                        <div class="content">
                                            <div class="title">Work Schedules</div>
                                            <div class="subtitle">Set department schedules</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('reports.index') }}" class="quick-action">
                                        <i class="bi bi-file-text"></i>
                                        <div class="content">
                                            <div class="title">Reports</div>
                                            <div class="subtitle">Generate HR reports</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary Chart -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Attendance Overview (Last 7 Days)</h5>
                            <select class="form-select form-select-sm w-auto" id="attendanceChartPeriod">
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceChart" style="height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activities</h5>
                        </div>
                        <div class="card-body p-0">
                            @forelse($recentActivities ?? [] as $activity)
                            <div class="recent-item d-flex align-items-center">
                                <div class="me-3">
                                    @if($activity['type'] == 'attendance')
                                        <i class="bi bi-clock-history text-primary"></i>
                                    @elseif($activity['type'] == 'holiday')
                                        <i class="bi bi-calendar-event text-success"></i>
                                    @elseif($activity['type'] == 'payroll')
                                        <i class="bi bi-cash-stack text-warning"></i>
                                    @else
                                        <i class="bi bi-person text-info"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $activity['title'] }}</div>
                                    <small class="text-muted">{{ $activity['description'] }}</small>
                                </div>
                                <div class="text-muted small">
                                    {{ $activity['time'] }}
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">
                                No recent activities
                            </div>
                            @endforelse
                        </div>
                        <div class="card-footer bg-white text-center">
                            <a href="#" class="text-decoration-none small">View All Activities <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Widgets -->
        <div class="col-lg-4">
            <!-- Holiday Widget -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-heart me-2 text-danger"></i>Upcoming Holidays</h5>
                    <a href="{{ route('holidays.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($upcomingHolidays ?? [] as $holiday)
                    <div class="recent-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">{{ $holiday['name'] }}</div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($holiday['date'])->format('M d, Y') }}
                                ({{ \Carbon\Carbon::parse($holiday['date'])->diffForHumans() }})
                            </small>
                        </div>
                        <span class="badge-holiday {{ $holiday['type'] }}">
                            {{ ucfirst($holiday['type']) }}
                            @if($holiday['rate'] > 1)
                                <span class="ms-1">({{ $holiday['rate'] }}x)</span>
                            @endif
                        </span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        No upcoming holidays
                    </div>
                    @endforelse
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between small">
                        <span>Regular Holidays: <span class="fw-bold">{{ $holidayStats['regular'] ?? 0 }}</span></span>
                        <span>Special: <span class="fw-bold">{{ $holidayStats['special'] ?? 0 }}</span></span>
                        <span>Total: <span class="fw-bold">{{ $holidayStats['total'] ?? 0 }}</span></span>
                    </div>
                </div>
            </div>

            <!-- Payroll Summary Widget -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cash me-2 text-success"></i>Payroll Summary</h5>
                    <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
                <div class="card-body">
<!-- Around line 220-240, update this section -->
@if($currentPayrollData ?? null)
<div class="mb-3">
    <div class="d-flex justify-content-between mb-1">
        <span>Current Period</span>
        <span class="fw-bold">{{ $currentPayrollData['name'] }}</span>
    </div>
    <div class="d-flex justify-content-between small text-muted">
        <span>{{ $currentPayrollData['start_date']->format('M d') }}</span>
        <span>{{ $currentPayrollData['end_date']->format('M d, Y') }}</span>
    </div>
</div>

<div class="mb-3">
    <div class="d-flex justify-content-between mb-1">
        <span>Processing Progress</span>
        <span class="fw-bold">{{ $currentPayrollData['progress'] }}%</span>
    </div>
    <div class="progress progress-sm">
        <div class="progress-bar bg-success" style="width: {{ $currentPayrollData['progress'] }}%"></div>
    </div>
</div>
@endif

                    <div class="row g-0 text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <div class="h5 mb-0 fw-bold">₱{{ number_format($payrollStats['total_payroll'] ?? 0) }}</div>
                                <small class="text-muted">Total Payroll</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <div class="h5 mb-0 fw-bold">{{ $payrollStats['employees_processed'] ?? 0 }}</div>
                                <small class="text-muted">Processed</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div>
                                <div class="h5 mb-0 fw-bold">{{ $payrollStats['pending'] ?? 0 }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Late Deductions</span>
                        <span class="text-danger">-₱{{ number_format($payrollStats['late_deductions'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Absence Deductions</span>
                        <span class="text-danger">-₱{{ number_format($payrollStats['absence_deductions'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Holiday Pay</span>
                        <span class="text-success">+₱{{ number_format($payrollStats['holiday_pay'] ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Department Distribution -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Department Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" style="height: 200px;"></canvas>
                    <div class="mt-3">
                        @foreach($departmentStats ?? [] as $dept)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $dept['name'] }}</span>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2">{{ $dept['count'] }}</span>
                                <div class="progress progress-sm" style="width: 100px;">
                                    <div class="progress-bar bg-info" style="width: {{ $dept['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Database</span>
                        <span class="text-success"><i class="bi bi-check-circle"></i> Connected</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Last Backup</span>
                        <span>{{ $systemStatus['last_backup'] ?? 'Today, 2:30 AM' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Storage Used</span>
                        <span>{{ $systemStatus['storage_used'] ?? '2.4 GB / 10 GB' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Active Users</span>
                        <span>{{ $systemStatus['active_users'] ?? '1' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        document.getElementById('liveClock').textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Attendance Chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($attendanceChart['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
            datasets: [{
                label: 'Present',
                data: {!! json_encode($attendanceChart['present'] ?? [65, 59, 80, 81, 56, 55, 40]) !!},
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Late',
                data: {!! json_encode($attendanceChart['late'] ?? [15, 20, 12, 18, 25, 10, 8]) !!},
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                }
            }
        }
    });

    // Department Chart
    const deptCtx = document.getElementById('departmentChart').getContext('2d');
    const deptData = {!! json_encode($departmentChart ?? []) !!};
    new Chart(deptCtx, {
        type: 'doughnut',
        data: {
            labels: deptData.labels || ['Shop', 'CT Print', 'Ecotrade', 'JCT'],
            datasets: [{
                data: deptData.data || [12, 8, 15, 10],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });

    // Chart period change handler
    document.getElementById('attendanceChartPeriod').addEventListener('change', function() {
        // Fetch new data based on selected period
        fetch(`/dashboard/attendance-data?days=${this.value}`)
            .then(response => response.json())
            .then(data => {
                attendanceChart.data.labels = data.labels;
                attendanceChart.data.datasets[0].data = data.present;
                attendanceChart.data.datasets[1].data = data.late;
                attendanceChart.update();
            });
    });
</script>
@endsection
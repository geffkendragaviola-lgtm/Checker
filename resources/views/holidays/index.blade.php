{{-- resources/views/payroll/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Payroll Management')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<style>
    .payroll-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .payroll-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .payroll-card.open {
        border-left: 4px solid #28a745;
    }
    .payroll-card.processing {
        border-left: 4px solid #ffc107;
    }
    .payroll-card.closed {
        border-left: 4px solid #6c757d;
        opacity: 0.8;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .employee-payroll-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .employee-payroll-row:hover {
        background-color: #f8f9fa;
    }
    .payroll-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .payroll-badge.open {
        background-color: #d4edda;
        color: #155724;
    }
    .payroll-badge.processing {
        background-color: #fff3cd;
        color: #856404;
    }
    .payroll-badge.closed {
        background-color: #e2e3e5;
        color: #383d41;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .tab-content {
        background: white;
        padding: 20px;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3"><i class="bi bi-cash-stack me-2"></i>Payroll Management</h1>
                <div>
                    <button class="btn btn-success me-2" onclick="createPayrollPeriod()">
                        <i class="bi bi-plus-circle me-2"></i>New Payroll Period
                    </button>
                    <button class="btn btn-primary" onclick="processPayroll()">
                        <i class="bi bi-gear me-2"></i>Process Payroll
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <p class="mb-1">Total Payroll (Month)</p>
                <h3 class="stat-number" id="totalPayroll">₱0</h3>
                <small>This month</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <p class="mb-1">Employees Processed</p>
                <h3 class="stat-number" id="employeesProcessed">0</h3>
                <small>Current period</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <p class="mb-1">Pending Payroll</p>
                <h3 class="stat-number" id="pendingPayroll">0</h3>
                <small>Open periods</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <p class="mb-1">Total Deductions</p>
                <h3 class="stat-number" id="totalDeductions">₱0</h3>
                <small>Current period</small>
            </div>
        </div>
    </div>

    <!-- Main Tabs -->
    <ul class="nav nav-pills mb-3" id="payrollTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="periods-tab" data-bs-toggle="pill" data-bs-target="#periods" type="button">
                <i class="bi bi-calendar-range me-2"></i>Payroll Periods
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="employees-tab" data-bs-toggle="pill" data-bs-target="#employees" type="button">
                <i class="bi bi-people me-2"></i>Employee Payroll
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="deductions-tab" data-bs-toggle="pill" data-bs-target="#deductions" type="button">
                <i class="bi bi-calculator me-2"></i>Deductions & Benefits
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button">
                <i class="bi bi-file-text me-2"></i>Reports
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="payrollTabContent">
        <!-- Payroll Periods Tab -->
        <div class="tab-pane fade show active" id="periods" role="tabpanel">
            <div class="row">
                @foreach($payrollPeriods ?? [] as $period)
                <div class="col-md-4">
                    <div class="payroll-card {{ $period->status }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0">{{ $period->name }}</h5>
                            <span class="payroll-badge {{ $period->status }}">{{ ucfirst($period->status) }}</span>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-calendar me-1"></i>
                            {{ Carbon\Carbon::parse($period->start_date)->format('M d') }} - 
                            {{ Carbon\Carbon::parse($period->end_date)->format('M d, Y') }}
                        </p>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Processed</span>
                                <span>{{ $period->processed_count ?? 0 }}/{{ $period->total_employees ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $period->progress ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">₱{{ number_format($period->total_payroll ?? 0, 2) }}</span>
                                <small class="text-muted d-block">Total Payroll</small>
                            </div>
                            <div class="btn-group">
                                @if($period->status === 'open')
                                    <button class="btn btn-sm btn-outline-primary" onclick="processPeriod({{ $period->id }})">
                                        <i class="bi bi-play-fill"></i> Process
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline-secondary" onclick="viewPeriod({{ $period->id }})">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="exportPeriod({{ $period->id }})">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Employee Payroll Tab -->
        <div class="tab-pane fade" id="employees" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="periodSelect" class="form-select">
                        <option value="">Select Payroll Period</option>
                        @foreach($payrollPeriods ?? [] as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="deptFilter" class="form-select">
                        <option value="all">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchEmployee" placeholder="Search employee...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Days Worked</th>
                            <th>Gross Pay</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeePayrollTable">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Deductions Tab -->
        <div class="tab-pane fade" id="deductions" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Government Contributions</h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <span>SSS Contribution</span>
                                <span class="fw-bold" id="totalSSS">₱0</span>
                            </div>
                            <div class="summary-item">
                                <span>PhilHealth</span>
                                <span class="fw-bold" id="totalPhilHealth">₱0</span>
                            </div>
                            <div class="summary-item">
                                <span>Pag-IBIG</span>
                                <span class="fw-bold" id="totalPagIbig">₱0</span>
                            </div>
                            <div class="summary-item">
                                <span>Withholding Tax</span>
                                <span class="fw-bold" id="totalTax">₱0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Other Deductions</h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <span>Late Deductions</span>
                                <span class="fw-bold" id="totalLateDeductions">₱0</span>
                            </div>
                            <div class="summary-item">
                                <span>Absence Deductions</span>
                                <span class="fw-bold" id="totalAbsenceDeductions">₱0</span>
                            </div>
                            <div class="summary-item">
                                <span>Cash Advances</span>
                                <span class="fw-bold" id="totalCashAdvances">₱0</span>
                            </div>
                            <div class="summary-item">
                                <span>Loan Payments</span>
                                <span class="fw-bold" id="totalLoans">₱0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Contribution Settings</h5>
                </div>
                <div class="card-body">
                    <form id="contributionForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SSS Employer Share (%)</label>
                                <input type="number" class="form-control" name="sss_employer" value="8.5" step="0.1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SSS Employee Share (%)</label>
                                <input type="number" class="form-control" name="sss_employee" value="4.5" step="0.1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">PhilHealth (%)</label>
                                <input type="number" class="form-control" name="philhealth" value="3.0" step="0.1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pag-IBIG (Fixed)</label>
                                <input type="number" class="form-control" name="pagibig" value="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Minimum Wage</label>
                                <input type="number" class="form-control" name="min_wage" value="570">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tax Table</label>
                                <select class="form-select" name="tax_table">
                                    <option value="2026">2026 BIR Table</option>
                                    <option value="2025">2025 BIR Table</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div class="tab-pane fade" id="reports" role="tabpanel">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-pdf text-danger display-4"></i>
                            <h6 class="mt-2">Payroll Summary Report</h6>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="generateReport('summary')">
                                <i class="bi bi-download"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-excel text-success display-4"></i>
                            <h6 class="mt-2">Employee Payslips</h6>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="generateReport('payslips')">
                                <i class="bi bi-download"></i> Generate All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-spreadsheet text-info display-4"></i>
                            <h6 class="mt-2">Government Remittance</h6>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="generateReport('remittance')">
                                <i class="bi bi-download"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Payroll Detail Modal -->
<div class="modal fade" id="employeePayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="employeePayrollBody">
                <!-- Populated via JS -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="printPayslip()">
                    <i class="bi bi-printer"></i> Print Payslip
                </button>
                <button class="btn btn-primary" onclick="downloadPayslip()">
                    <i class="bi bi-download"></i> Download
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Payroll Period Modal -->
<div class="modal fade" id="payrollPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Payroll Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="payrollPeriodForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Period Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cutoff Date</label>
                        <input type="date" class="form-control" name="cutoff_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Period</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadEmployeePayroll();
        loadDeductionSummary();
    });

    function loadEmployeePayroll() {
        const periodId = document.getElementById('periodSelect').value;
        const deptId = document.getElementById('deptFilter').value;
        const search = document.getElementById('searchEmployee').value;

        fetch(`/payroll/employees?period=${periodId}&department=${deptId}&search=${search}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('employeePayrollTable');
                    tbody.innerHTML = data.employees.map(emp => `
                        <tr class="employee-payroll-row" onclick="viewEmployeePayroll(${emp.id})">
                            <td>${emp.employee_code}</td>
                            <td>${emp.name}</td>
                            <td>${emp.department}</td>
                            <td>${emp.days_worked}</td>
                            <td>₱${formatNumber(emp.gross_pay)}</td>
                            <td>₱${formatNumber(emp.total_deductions)}</td>
                            <td class="fw-bold">₱${formatNumber(emp.net_pay)}</td>
                            <td>
                                <span class="payroll-badge ${emp.status}">${emp.status}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewPayslip(${emp.id})">
                                    <i class="bi bi-file-text"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }
            });
    }

    function loadDeductionSummary() {
        const periodId = document.getElementById('periodSelect').value;
        
        fetch(`/payroll/deductions/summary?period=${periodId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('totalSSS').textContent = '₱' + formatNumber(data.sss);
                    document.getElementById('totalPhilHealth').textContent = '₱' + formatNumber(data.philhealth);
                    document.getElementById('totalPagIbig').textContent = '₱' + formatNumber(data.pagibig);
                    document.getElementById('totalTax').textContent = '₱' + formatNumber(data.tax);
                    document.getElementById('totalLateDeductions').textContent = '₱' + formatNumber(data.late);
                    document.getElementById('totalAbsenceDeductions').textContent = '₱' + formatNumber(data.absence);
                    document.getElementById('totalCashAdvances').textContent = '₱' + formatNumber(data.cash_advance);
                    document.getElementById('totalLoans').textContent = '₱' + formatNumber(data.loans);
                }
            });
    }

    function viewEmployeePayroll(id) {
        fetch(`/payroll/employee/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('employeePayrollBody').innerHTML = data.html;
                    const modal = new bootstrap.Modal(document.getElementById('employeePayrollModal'));
                    modal.show();
                }
            });
    }

    function createPayrollPeriod() {
        const modal = new bootstrap.Modal(document.getElementById('payrollPeriodModal'));
        modal.show();
    }

    document.getElementById('payrollPeriodForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/payroll/periods', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('payrollPeriodModal')).hide();
                location.reload();
            }
        });
    });

    function processPayroll() {
        if (!confirm('Process payroll for current period?')) return;
        
        fetch('/payroll/process', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payroll processed successfully!');
                location.reload();
            }
        });
    }

    function formatNumber(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Event listeners
    document.getElementById('periodSelect').addEventListener('change', function() {
        loadEmployeePayroll();
        loadDeductionSummary();
    });

    document.getElementById('deptFilter').addEventListener('change', loadEmployeePayroll);
    document.getElementById('searchEmployee').addEventListener('keyup', debounce(loadEmployeePayroll, 500));

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
</script>
@endsection
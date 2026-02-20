<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkSchedule;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ContributionTypeSeeder::class,
            EmployeeSeeder::class,
        ]);

        // First, clear existing data (optional - be careful!)
        // WorkSchedule::truncate();
        // Department::truncate();
        // Employee::truncate();

        // Create work schedules based on the actual department schedules
        $schedules = [
            [
                'name' => 'Shop Schedule',
                'work_start_time' => '08:00:00',
                'work_end_time' => '17:00:00',
                'break_start_time' => '12:00:00',
                'break_end_time' => '13:00:00',
                'grace_period_minutes' => 15,
                'is_working_day' => true,
                // Remove description if column doesn't exist
            ],
            [
                'name' => 'CT Print Shop / Ecotrade Schedule',
                'work_start_time' => '08:30:00',
                'work_end_time' => '17:30:00',
                'break_start_time' => '12:00:00',
                'break_end_time' => '13:00:00',
                'grace_period_minutes' => 15,
                'is_working_day' => true,
            ],
            [
                'name' => 'JCT Schedule',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
                'break_start_time' => '12:00:00',
                'break_end_time' => '13:00:00',
                'grace_period_minutes' => 15,
                'is_working_day' => true,
            ],
            [
                'name' => 'Half Day Schedule',
                'work_start_time' => '08:00:00',
                'work_end_time' => '12:00:00',
                'break_start_time' => null,
                'break_end_time' => null,
                'grace_period_minutes' => 10,
                'is_working_day' => true,
            ],
            [
                'name' => 'Weekend Schedule',
                'work_start_time' => '09:00:00',
                'work_end_time' => '13:00:00',
                'break_start_time' => null,
                'break_end_time' => null,
                'grace_period_minutes' => 10,
                'is_working_day' => false,
            ]
        ];

        foreach ($schedules as $schedule) {
            WorkSchedule::create($schedule);
        }

        $this->command->info('Work schedules created successfully!');

        // Create departments based on the actual departments in logs
        $departments = [
            [
                'name' => 'Shop',
                'branch_code' => 'SHOP',
                'schedule_id' => 1, // 8:00 AM - 5:00 PM
            ],
            [
                'name' => 'CT Print Shop',
                'branch_code' => 'CT',
                'schedule_id' => 2, // 8:30 AM - 5:30 PM
            ],
            [
                'name' => 'Ecotrade',
                'branch_code' => 'ECO',
                'schedule_id' => 2, // 8:30 AM - 5:30 PM
            ],
            [
                'name' => 'Shop / Eco',
                'branch_code' => 'SHOPECO',
                'schedule_id' => 2, // 8:30 AM - 5:30 PM
            ],
            [
                'name' => 'JCT',
                'branch_code' => 'JCT',
                'schedule_id' => 3, // 9:00 AM - 6:00 PM
            ]
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        $this->command->info('Departments created successfully!');

        // Extract unique employees from logs and create employee records
        $employees = [
            // Shop Department (ID: 1)
            ['employee_code' => 'SHOP2025-23', 'first_name' => 'MELVIN', 'last_name' => 'ABONG', 'department_id' => 1, 'position' => 'Staff'],
            ['employee_code' => 'SHOP2025-08', 'first_name' => 'Darius', 'last_name' => 'Manlupig', 'department_id' => 1, 'position' => 'Staff'],
            ['employee_code' => 'SHOP2025-20', 'first_name' => 'Ronnie', 'last_name' => 'Tekong', 'department_id' => 1, 'position' => 'Staff'],
            ['employee_code' => 'SHOP2025-18', 'first_name' => 'Consorcio', 'last_name' => 'Mariquit', 'department_id' => 1, 'position' => 'Staff'],
            ['employee_code' => 'SHOP2025-24', 'first_name' => 'Ulysses', 'last_name' => 'Bangcong', 'department_id' => 1, 'position' => 'Staff'],
            ['employee_code' => 'SHOP2025-22', 'first_name' => 'Jay-ar', 'last_name' => 'Magdale', 'department_id' => 1, 'position' => 'Staff'],
            
            // CT Print Shop Department (ID: 2)
            ['employee_code' => 'CT2025-11', 'first_name' => 'Mylin', 'last_name' => 'Abong', 'department_id' => 2, 'position' => 'Staff'],
            ['employee_code' => 'CT2025-10', 'first_name' => 'Reggie Ann', 'last_name' => 'Micabalo', 'department_id' => 2, 'position' => 'Staff'],
            ['employee_code' => 'CT2025-13', 'first_name' => 'Roselyn', 'last_name' => 'Mariquit', 'department_id' => 2, 'position' => 'Staff'],
            ['employee_code' => 'JC2025-16', 'first_name' => 'ALORNA', 'last_name' => 'LEURAG', 'department_id' => 2, 'position' => 'Staff'],
            
            // Ecotrade Department (ID: 3)
            ['employee_code' => 'JC2025-02', 'first_name' => 'Daryl', 'last_name' => 'CONCERMAN', 'department_id' => 3, 'position' => 'Staff'],
            ['employee_code' => 'ECO2025-21', 'first_name' => 'Judeirick', 'last_name' => 'Ligutom', 'department_id' => 3, 'position' => 'Staff'],
            ['employee_code' => 'JC2025-06', 'first_name' => 'Marijane', 'last_name' => 'Reysoma', 'department_id' => 3, 'position' => 'Staff'],
            ['employee_code' => 'ECO2025-04', 'first_name' => 'Regie', 'last_name' => 'Miñoza', 'department_id' => 3, 'position' => 'Staff'],
            ['employee_code' => 'JC2025-01', 'first_name' => 'Julie Anne', 'last_name' => 'Caballero', 'department_id' => 3, 'position' => 'Staff'],
            ['employee_code' => 'ECO2025-05', 'first_name' => 'Ryan', 'last_name' => 'Fama', 'department_id' => 3, 'position' => 'Staff'],
            ['employee_code' => 'ECO2025-26', 'first_name' => 'ANTHONY', 'last_name' => 'BANO', 'department_id' => 3, 'position' => 'Staff'],
            
            // Shop / Eco Department (ID: 4)
            ['employee_code' => 'JCT2025-14', 'first_name' => 'John lee', 'last_name' => 'Macalaguing', 'department_id' => 4, 'position' => 'Staff'],
            
            // JCT Department (ID: 5)
            ['employee_code' => 'JCT2025-19', 'first_name' => 'Loui Givney', 'last_name' => 'Vequizo', 'department_id' => 5, 'position' => 'Staff'],
            ['employee_code' => 'JCT2025-12', 'first_name' => 'RONALYN', 'last_name' => 'TACAISAN', 'department_id' => 5, 'position' => 'Staff'],
            ['employee_code' => 'JCT2025-07', 'first_name' => 'Lovely', 'last_name' => 'Patua', 'department_id' => 5, 'position' => 'Staff'],
            ['employee_code' => 'JC2025-09', 'first_name' => 'Medel', 'last_name' => 'Abella', 'department_id' => 5, 'position' => 'Staff'],
        ];

        $dailyRate = 500; // Base daily rate
        
        foreach ($employees as $emp) {
            Employee::create([
                'employee_code' => $emp['employee_code'],
                'first_name' => $emp['first_name'],
                'last_name' => $emp['last_name'],
                'middle_name' => null,
                'department_id' => $emp['department_id'],
                'position' => $emp['position'],
                'daily_rate' => $dailyRate + (rand(0, 10) * 50), // Random rate between 500-1000
                'hire_date' => now()->subMonths(rand(1, 24))->subDays(rand(0, 28)),
                'employment_status' => 'active',
                'email' => strtolower($emp['first_name'] . '.' . str_replace(' ', '', $emp['last_name']) . '@company.com'),
                'phone' => '09' . rand(100000000, 999999999),
                'address' => 'Iligan City, Lanao del Norte'
            ]);
        }

        $this->command->info('Employees created successfully!');

        // Create sample holidays for 2026
        $holidays = [
            [
                'name' => 'New Year\'s Day',
                'holiday_date' => '2026-01-01',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Maundy Thursday',
                'holiday_date' => '2026-04-02',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Good Friday',
                'holiday_date' => '2026-04-03',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Araw ng Kagitingan',
                'holiday_date' => '2026-04-09',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Labor Day',
                'holiday_date' => '2026-05-01',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Independence Day',
                'holiday_date' => '2026-06-12',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'National Heroes Day',
                'holiday_date' => '2026-08-31',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Bonifacio Day',
                'holiday_date' => '2026-11-30',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Christmas Day',
                'holiday_date' => '2026-12-25',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Rizal Day',
                'holiday_date' => '2026-12-30',
                'type' => 'regular',
                'schedule_id' => null,
                'rate_multiplier' => 2.0,
                'is_paid' => true,
                'department_id' => null
            ],
            // Special non-working holidays
            [
                'name' => 'Chinese New Year',
                'holiday_date' => '2026-02-17',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'EDSA People Power Anniversary',
                'holiday_date' => '2026-02-25',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Black Saturday',
                'holiday_date' => '2026-04-04',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Ninoy Aquino Day',
                'holiday_date' => '2026-08-21',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'All Saints Day',
                'holiday_date' => '2026-11-01',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'All Souls Day',
                'holiday_date' => '2026-11-02',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'Feast of the Immaculate Conception',
                'holiday_date' => '2026-12-08',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ],
            [
                'name' => 'New Year\'s Eve',
                'holiday_date' => '2026-12-31',
                'type' => 'special',
                'schedule_id' => null,
                'rate_multiplier' => 1.3,
                'is_paid' => true,
                'department_id' => null
            ]
        ];

        foreach ($holidays as $holiday) {
            Holiday::create($holiday);
        }

        $this->command->info('Holidays created successfully!');

        // Create sample payroll periods for 2026
        $payrollPeriods = [
            [
                'name' => 'January 1-15, 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-01-15',
                'status' => 'closed',
                'cutoff_date' => '2026-01-15'
            ],
            [
                'name' => 'January 16-31, 2026',
                'start_date' => '2026-01-16',
                'end_date' => '2026-01-31',
                'status' => 'closed',
                'cutoff_date' => '2026-01-31'
            ],
            [
                'name' => 'February 1-15, 2026',
                'start_date' => '2026-02-01',
                'end_date' => '2026-02-15',
                'status' => 'processing',
                'cutoff_date' => '2026-02-15'
            ],
            [
                'name' => 'February 16-28, 2026',
                'start_date' => '2026-02-16',
                'end_date' => '2026-02-28',
                'status' => 'open',
                'cutoff_date' => '2026-02-28'
            ],
            [
                'name' => 'March 1-15, 2026',
                'start_date' => '2026-03-01',
                'end_date' => '2026-03-15',
                'status' => 'open',
                'cutoff_date' => '2026-03-15'
            ]
        ];

        foreach ($payrollPeriods as $period) {
            \App\Models\PayrollPeriod::create($period);
        }

        $this->command->info('Payroll periods created successfully!');

        // Create sample schedule overrides
        $overrides = [
            [
                'override_date' => '2026-02-14',
                'department_id' => 1, // Shop
                'schedule_id' => 4, // Half Day Schedule
                'reason' => 'Valentine\'s Day half-day',
                'is_global' => false
            ],
            [
                'override_date' => '2026-02-25',
                'department_id' => null, // All departments
                'schedule_id' => 5, // Weekend Schedule
                'reason' => 'EDSA People Power Anniversary',
                'is_global' => true
            ],
            [
                'override_date' => '2026-03-15',
                'department_id' => 3, // Ecotrade
                'schedule_id' => 4, // Half Day Schedule
                'reason' => 'Company Foundation Day',
                'is_global' => false
            ],
            [
                'override_date' => '2026-04-09',
                'department_id' => null,
                'schedule_id' => 5,
                'reason' => 'Araw ng Kagitingan',
                'is_global' => true
            ]
        ];

        foreach ($overrides as $override) {
            \App\Models\ScheduleOverride::create($override);
        }

        $this->command->info('Schedule overrides created successfully!');
        
        $this->command->info('====================================');
        $this->command->info('Database seeded successfully!');
        $this->command->info('====================================');
        $this->command->info('Admin login: admin@hrsystem.com / password');
        $this->command->info('Total Work Schedules: ' . count($schedules));
        $this->command->info('Total Departments: ' . count($departments));
        $this->command->info('Total Employees: ' . count($employees));
        $this->command->info('Total Holidays: ' . count($holidays));
        $this->command->info('Total Payroll Periods: ' . count($payrollPeriods));
        $this->command->info('====================================');
    }
}
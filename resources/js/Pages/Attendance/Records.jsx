import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';

const dummySummary = [
    {
        id: 1,
        employee: 'Juan Dela Cruz',
        department: 'Engineering',
        workdaysRendered: 10,
        totalAbsences: 0,
        totalLateMinutes: 35,
        totalUndertimeMinutes: 0,
        totalOvertimeMinutes: 120,
        totalMissedLogs: 0,
    },
    {
        id: 2,
        employee: 'Maria Santos',
        department: 'HR',
        workdaysRendered: 9,
        totalAbsences: 1,
        totalLateMinutes: 0,
        totalUndertimeMinutes: 30,
        totalOvertimeMinutes: 0,
        totalMissedLogs: 2,
    },
    {
        id: 3,
        employee: 'Pedro Reyes',
        department: 'Finance',
        workdaysRendered: 8.5,
        totalAbsences: 0.5,
        totalLateMinutes: 60,
        totalUndertimeMinutes: 15,
        totalOvertimeMinutes: 45,
        totalMissedLogs: 1,
    },
    {
        id: 4,
        employee: 'Ana Garcia',
        department: 'Operations',
        workdaysRendered: 11,
        totalAbsences: 0,
        totalLateMinutes: 0,
        totalUndertimeMinutes: 0,
        totalOvertimeMinutes: 0,
        totalMissedLogs: 0,
    },
];

const dummyDetailsByEmployee = {
    1: [
        {
            id: 1,
            date: '2026-02-01',
            timeInAm: '08:10',
            timeOutLunch: '12:00',
            timeInPm: '13:00',
            timeOutPm: '17:05',
            lateAmMinutes: 10,
            latePmMinutes: 0,
            totalLateMinutes: 10,
            overtimeMinutes: 5,
            status: 'Late',
            missingLogs: false,
        },
        {
            id: 2,
            date: '2026-02-02',
            timeInAm: '08:00',
            timeOutLunch: '12:00',
            timeInPm: '13:00',
            timeOutPm: '17:30',
            lateAmMinutes: 0,
            latePmMinutes: 0,
            totalLateMinutes: 0,
            overtimeMinutes: 30,
            status: 'Complete',
            missingLogs: false,
        },
    ],
    2: [
        {
            id: 3,
            date: '2026-02-01',
            timeInAm: null,
            timeOutLunch: null,
            timeInPm: null,
            timeOutPm: null,
            lateAmMinutes: 0,
            latePmMinutes: 0,
            totalLateMinutes: 0,
            overtimeMinutes: 0,
            status: 'Absent (whole day)',
            missingLogs: true,
        },
        {
            id: 4,
            date: '2026-02-02',
            timeInAm: '08:00',
            timeOutLunch: '12:00',
            timeInPm: '13:20',
            timeOutPm: '17:00',
            lateAmMinutes: 0,
            latePmMinutes: 20,
            totalLateMinutes: 20,
            overtimeMinutes: 0,
            status: 'Late PM',
            missingLogs: false,
        },
    ],
    3: [
        {
            id: 5,
            date: '2026-02-01',
            timeInAm: '08:30',
            timeOutLunch: '12:00',
            timeInPm: '13:00',
            timeOutPm: '17:00',
            lateAmMinutes: 30,
            latePmMinutes: 0,
            totalLateMinutes: 30,
            overtimeMinutes: 0,
            status: 'Late AM',
            missingLogs: false,
        },
        {
            id: 6,
            date: '2026-02-02',
            timeInAm: '08:00',
            timeOutLunch: '12:00',
            timeInPm: null,
            timeOutPm: '17:00',
            lateAmMinutes: 0,
            latePmMinutes: 0,
            totalLateMinutes: 0,
            overtimeMinutes: 0,
            status: 'Absent PM',
            missingLogs: true,
        },
    ],
    4: [
        {
            id: 7,
            date: '2026-02-01',
            timeInAm: '08:00',
            timeOutLunch: '12:00',
            timeInPm: '13:00',
            timeOutPm: '17:00',
            lateAmMinutes: 0,
            latePmMinutes: 0,
            totalLateMinutes: 0,
            overtimeMinutes: 0,
            status: 'Complete',
            missingLogs: false,
        },
    ],
};

function formatMinutesToTime(totalMinutes) {
    if (!totalMinutes || totalMinutes <= 0) return '00:00';
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}

function getSummaryRowClass(record) {
    if (record.totalMissedLogs > 0) return 'bg-red-50';
    if (record.totalLateMinutes > 0) return 'bg-orange-50';
    return '';
}

function getDetailRowClass(row) {
    if (row.missingLogs) return 'bg-red-50';
    if (row.totalLateMinutes > 0) return 'bg-orange-50';
    return '';
}

function FilterSelect({ label, value, onChange, options }) {
    return (
        <div>
            <label className="mb-1 block text-xs font-medium text-slate-600">{label}</label>
            <select
                value={value}
                onChange={(e) => onChange(e.target.value)}
                className="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-[#1E3A8A] focus:outline-none focus:ring-1 focus:ring-[#1E3A8A]"
            >
                {options.map((opt) => (
                    <option key={opt.value} value={opt.value}>
                        {opt.label}
                    </option>
                ))}
            </select>
        </div>
    );
}

export default function Records() {
    const [period, setPeriod] = useState('');
    const [department, setDepartment] = useState('');
    const [employee, setEmployee] = useState('');
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');
    const [selectedEmployeeId, setSelectedEmployeeId] = useState(null);

    const filteredSummary = useMemo(() => {
        return dummySummary.filter((item) => {
            if (department && item.department.toLowerCase() !== department.toLowerCase()) return false;
            if (employee && !item.employee.toLowerCase().includes(employee.toLowerCase())) return false;
            return true;
        });
    }, [department, employee]);

    const selectedEmployee = useMemo(
        () => filteredSummary.find((e) => e.id === selectedEmployeeId) || null,
        [filteredSummary, selectedEmployeeId],
    );

    const selectedDetails = useMemo(() => {
        if (!selectedEmployeeId) return [];
        // In a real implementation you would also filter by payroll period and date range here
        return dummyDetailsByEmployee[selectedEmployeeId] || [];
    }, [selectedEmployeeId]);

    return (
        <AdminLayout title="Attendance">
            <Head title="Attendance" />

            <div className="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div className="mb-3 flex items-center justify-between gap-3">
                    <div className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Filter by
                    </div>
                    <Link
                        href={route('admin.attendance.upload-logs')}
                        className="inline-flex items-center gap-1.5 rounded-md bg-[#1E3A8A] px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-[#1E3A8A]/90"
                    >
                        <svg
                            className="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fillRule="evenodd"
                                d="M10 3a.75.75 0 01.75.75V11h3.5a.75.75 0 010 1.5h-3.5v3.75a.75.75 0 01-1.5 0V12.5h-3.5a.75.75 0 010-1.5h3.5V3.75A.75.75 0 0110 3z"
                                clipRule="evenodd"
                            />
                        </svg>
                        <span>Upload Logs</span>
                    </Link>
                </div>
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <FilterSelect
                        label="Payroll Period"
                        value={period}
                        onChange={setPeriod}
                        options={[
                            { value: '', label: 'All Periods' },
                            { value: '2026-02-01', label: 'Feb 1–15, 2026' },
                            { value: '2026-01-16', label: 'Jan 16–31, 2026' },
                        ]}
                    />
                    <FilterSelect
                        label="Department"
                        value={department}
                        onChange={setDepartment}
                        options={[
                            { value: '', label: 'All Departments' },
                            { value: 'Engineering', label: 'Engineering' },
                            { value: 'HR', label: 'HR' },
                            { value: 'Finance', label: 'Finance' },
                            { value: 'Operations', label: 'Operations' },
                        ]}
                    />
                    <div>
                        <label className="mb-1 block text-xs font-medium text-slate-600">
                            Employee
                        </label>
                        <input
                            type="text"
                            placeholder="Type employee name"
                            value={employee}
                            onChange={(e) => setEmployee(e.target.value)}
                            className="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-[#1E3A8A] focus:outline-none focus:ring-1 focus:ring-[#1E3A8A]"
                        />
                    </div>
                    <div>
                        <label className="mb-1 block text-xs font-medium text-slate-600">Date From</label>
                        <input
                            type="date"
                            value={dateFrom}
                            onChange={(e) => setDateFrom(e.target.value)}
                            className="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-[#1E3A8A] focus:outline-none focus:ring-1 focus:ring-[#1E3A8A]"
                        />
                    </div>
                    <div>
                        <label className="mb-1 block text-xs font-medium text-slate-600">Date To</label>
                        <input
                            type="date"
                            value={dateTo}
                            onChange={(e) => setDateTo(e.target.value)}
                            className="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-[#1E3A8A] focus:outline-none focus:ring-1 focus:ring-[#1E3A8A]"
                        />
                    </div>
                </div>
            </div>

            <div className="mb-3 flex items-center gap-4 text-xs text-slate-500">
                <span className="flex items-center gap-1.5">
                    <span className="inline-block h-3 w-3 rounded-sm border border-red-300 bg-red-100" />
                    Red – Missing Logs
                </span>
                <span className="flex items-center gap-1.5">
                    <span className="inline-block h-3 w-3 rounded-sm border border-orange-300 bg-orange-100" />
                    Orange – Has Lates
                </span>
                <span className="flex items-center gap-1.5">
                    <span className="inline-block h-3 w-3 rounded-sm border border-slate-300 bg-white" />
                    Normal – Complete
                </span>
            </div>

            <div className="mb-6 overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                <table className="min-w-full text-left text-sm">
                    <thead className="bg-[#1E3A8A] text-xs uppercase text-white">
                        <tr>
                            <th className="px-4 py-3 font-medium">Employee</th>
                            <th className="px-4 py-3 font-medium">Department</th>
                            <th className="px-4 py-3 font-medium">Workday Rendered</th>
                            <th className="px-4 py-3 font-medium">Total Absences</th>
                            <th className="px-4 py-3 font-medium">Total Late (HH:MM)</th>
                            <th className="px-4 py-3 font-medium">Total Undertime</th>
                            <th className="px-4 py-3 font-medium">Total Overtime</th>
                            <th className="px-4 py-3 font-medium">Total Missed Logs</th>
                            <th className="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {filteredSummary.map((item) => (
                            <tr
                                key={item.id}
                                className={`${getSummaryRowClass(item)} transition hover:bg-slate-50`}
                            >
                                <td className="whitespace-nowrap px-4 py-3 font-medium text-slate-900">
                                    {item.employee}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3 text-slate-600">
                                    {item.department}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                    {item.workdaysRendered}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                    {item.totalAbsences}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3">
                                    <span
                                        className={
                                            item.totalLateMinutes > 0
                                                ? 'font-semibold text-[#F59E0B]'
                                                : 'text-slate-400'
                                        }
                                    >
                                        {formatMinutesToTime(item.totalLateMinutes)}
                                    </span>
                                </td>
                                <td className="whitespace-nowrap px-4 py-3">
                                    {item.totalUndertimeMinutes > 0 ? (
                                        <span className="font-medium text-slate-700">
                                            {formatMinutesToTime(item.totalUndertimeMinutes)}
                                        </span>
                                    ) : (
                                        <span className="text-slate-400">00:00</span>
                                    )}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3">
                                    {item.totalOvertimeMinutes > 0 ? (
                                        <span className="font-medium text-slate-700">
                                            {formatMinutesToTime(item.totalOvertimeMinutes)}
                                        </span>
                                    ) : (
                                        <span className="text-slate-400">00:00</span>
                                    )}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3">
                                    {item.totalMissedLogs > 0 ? (
                                        <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                            {item.totalMissedLogs}
                                        </span>
                                    ) : (
                                        <span className="text-slate-400">0</span>
                                    )}
                                </td>
                                <td className="whitespace-nowrap px-4 py-3">
                                    <div className="flex items-center gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setSelectedEmployeeId(item.id)}
                                            className="rounded bg-[#1E3A8A] px-2.5 py-1 text-xs font-medium text-white transition hover:bg-[#1E3A8A]/80"
                                        >
                                            View
                                        </button>
                                        {(item.totalMissedLogs > 0 || item.totalLateMinutes > 0) && (
                                            <button
                                                type="button"
                                                className="rounded bg-[#F59E0B] px-2.5 py-1 text-xs font-medium text-white transition hover:bg-[#F59E0B]/80"
                                            >
                                                Generate Letter
                                            </button>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {filteredSummary.length === 0 && (
                            <tr>
                                <td
                                    colSpan={9}
                                    className="px-4 py-6 text-center text-sm text-slate-500"
                                >
                                    No attendance records found for the selected filters.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {selectedEmployee && (
                <div className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div className="mb-4 flex items-start justify-between gap-3">
                        <div>
                            <div className="text-sm font-semibold text-slate-800">
                                Detailed Attendance Breakdown
                            </div>
                            <div className="mt-0.5 text-xs text-slate-500">
                                {selectedEmployee.employee} &middot; {selectedEmployee.department}
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSelectedEmployeeId(null)}
                            className="rounded-md border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                        >
                            Close
                        </button>
                    </div>

                    <div className="overflow-x-auto rounded-lg border border-slate-200">
                        <table className="min-w-full text-left text-sm">
                            <thead className="bg-slate-50 text-xs uppercase text-slate-600">
                                <tr>
                                    <th className="px-4 py-3 font-medium">Date</th>
                                    <th className="px-4 py-3 font-medium">Time In AM</th>
                                    <th className="px-4 py-3 font-medium">Time Out Lunch</th>
                                    <th className="px-4 py-3 font-medium">Time In PM</th>
                                    <th className="px-4 py-3 font-medium">Time Out PM</th>
                                    <th className="px-4 py-3 font-medium">Late AM</th>
                                    <th className="px-4 py-3 font-medium">Late PM</th>
                                    <th className="px-4 py-3 font-medium">Total Late (HH:MM)</th>
                                    <th className="px-4 py-3 font-medium">Overtime</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {selectedDetails.map((row) => (
                                    <tr
                                        key={row.id}
                                        className={`${getDetailRowClass(row)} transition hover:bg-slate-50`}
                                    >
                                        <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                            {row.date}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                            {row.timeInAm ? (
                                                row.timeInAm
                                            ) : (
                                                <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                                    Missing Log
                                                </span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                            {row.timeOutLunch ? (
                                                row.timeOutLunch
                                            ) : (
                                                <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                                    Missing Log
                                                </span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                            {row.timeInPm ? (
                                                row.timeInPm
                                            ) : (
                                                <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                                    Missing Log
                                                </span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3 text-slate-700">
                                            {row.timeOutPm ? (
                                                row.timeOutPm
                                            ) : (
                                                <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                                    Missing Log
                                                </span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3">
                                            {row.lateAmMinutes > 0 ? (
                                                <span className="font-semibold text-[#F59E0B]">
                                                    {row.lateAmMinutes} min
                                                </span>
                                            ) : (
                                                <span className="text-slate-400">0</span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3">
                                            {row.latePmMinutes > 0 ? (
                                                <span className="font-semibold text-[#F59E0B]">
                                                    {row.latePmMinutes} min
                                                </span>
                                            ) : (
                                                <span className="text-slate-400">0</span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3">
                                            {row.totalLateMinutes > 0 ? (
                                                <span className="font-bold text-[#F59E0B]">
                                                    {formatMinutesToTime(row.totalLateMinutes)}
                                                </span>
                                            ) : (
                                                <span className="text-slate-400">00:00</span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3">
                                            {row.overtimeMinutes > 0 ? (
                                                <span className="font-medium text-slate-700">
                                                    {formatMinutesToTime(row.overtimeMinutes)}
                                                </span>
                                            ) : (
                                                <span className="text-slate-400">00:00</span>
                                            )}
                                        </td>
                                        <td className="whitespace-nowrap px-4 py-3 text-xs font-medium text-slate-700">
                                            {row.status}
                                        </td>
                                    </tr>
                                ))}
                                {selectedDetails.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={10}
                                            className="px-4 py-6 text-center text-sm text-slate-500"
                                        >
                                            No detailed logs available for this employee and period.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}

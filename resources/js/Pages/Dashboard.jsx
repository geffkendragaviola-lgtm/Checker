import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import {
    BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    PieChart, Pie, Cell, Legend,
} from 'recharts';

const COLORS = {
    primary: '#1E3A8A',
    success: '#16A34A',
    warning: '#F59E0B',
    danger: '#DC2626',
    slate: '#334155',
    blue: '#3B82F6',
};

function SummaryCard({ label, value, color = 'bg-white', textColor = 'text-[#334155]', accent }) {
    return (
        <div className={`rounded-lg border border-slate-200 ${color} p-5 shadow-sm`}>
            <div className="text-xs font-medium uppercase tracking-wide text-slate-500">
                {label}
            </div>
            <div className={`mt-2 text-2xl font-bold ${textColor}`}>{value}</div>
            {accent && (
                <div className={`mt-1 text-xs font-medium ${accent.color}`}>{accent.text}</div>
            )}
        </div>
    );
}

const dummyLateByDept = [
    { department: 'Engineering', lates: 12 },
    { department: 'HR', lates: 5 },
    { department: 'Finance', lates: 8 },
    { department: 'Operations', lates: 15 },
    { department: 'Marketing', lates: 3 },
];

const dummyAttendanceDist = [
    { name: 'Complete', value: 120 },
    { name: 'With Lates', value: 35 },
    { name: 'Missing Logs', value: 12 },
];

const PIE_COLORS = [COLORS.success, COLORS.warning, COLORS.danger];

export default function Dashboard() {
    return (
        <AdminLayout title="Dashboard">
            <Head title="Dashboard" />

            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
                <SummaryCard
                    label="Total Employees"
                    value="167"
                />
                <SummaryCard
                    label="Logs Uploaded (Current Period)"
                    value="1,248"
                />
                <SummaryCard
                    label="Employees with Lates"
                    value="35"
                    accent={{ text: '20.9% of total', color: 'text-[#F59E0B]' }}
                />
                <SummaryCard
                    label="Employees with Missing Logs"
                    value="12"
                    accent={{ text: '7.2% of total', color: 'text-[#DC2626]' }}
                />
                <SummaryCard
                    label="Payroll Status"
                    value="Open"
                    accent={{ text: 'Feb 1 – 15, 2026', color: 'text-[#16A34A]' }}
                />
            </div>

            <div className="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 className="mb-4 text-sm font-semibold text-[#334155]">
                        Late Count per Department
                    </h2>
                    <ResponsiveContainer width="100%" height={280}>
                        <BarChart data={dummyLateByDept} margin={{ top: 5, right: 20, left: 0, bottom: 5 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#E2E8F0" />
                            <XAxis dataKey="department" tick={{ fontSize: 12, fill: '#64748B' }} />
                            <YAxis tick={{ fontSize: 12, fill: '#64748B' }} />
                            <Tooltip
                                contentStyle={{ fontSize: 12, borderRadius: 8, border: '1px solid #E2E8F0' }}
                            />
                            <Bar dataKey="lates" fill={COLORS.primary} radius={[4, 4, 0, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 className="mb-4 text-sm font-semibold text-[#334155]">
                        Attendance Status Distribution
                    </h2>
                    <ResponsiveContainer width="100%" height={280}>
                        <PieChart>
                            <Pie
                                data={dummyAttendanceDist}
                                cx="50%"
                                cy="50%"
                                innerRadius={60}
                                outerRadius={100}
                                paddingAngle={3}
                                dataKey="value"
                                label={({ name, percent }) =>
                                    `${name} ${(percent * 100).toFixed(0)}%`
                                }
                            >
                                {dummyAttendanceDist.map((_, i) => (
                                    <Cell key={i} fill={PIE_COLORS[i % PIE_COLORS.length]} />
                                ))}
                            </Pie>
                            <Tooltip contentStyle={{ fontSize: 12, borderRadius: 8, border: '1px solid #E2E8F0' }} />
                            <Legend
                                verticalAlign="bottom"
                                iconType="circle"
                                wrapperStyle={{ fontSize: 12 }}
                            />
                        </PieChart>
                    </ResponsiveContainer>
                </div>
            </div>
        </AdminLayout>
    );
}

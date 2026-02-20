import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';

export default function Summary() {
    return (
        <AdminLayout title="Payroll Summary">
            <Head title="Payroll Summary" />
            <div className="text-sm text-slate-600">Empty page</div>
        </AdminLayout>
    );
}

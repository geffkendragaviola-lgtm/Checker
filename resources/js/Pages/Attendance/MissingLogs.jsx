import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';

export default function MissingLogs() {
    return (
        <AdminLayout title="Missing Logs Report">
            <Head title="Missing Logs Report" />
            <div className="text-sm text-slate-600">Empty page</div>
        </AdminLayout>
    );
}

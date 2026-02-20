import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';

export default function Index() {
    return (
        <AdminLayout title="Work Schedules">
            <Head title="Work Schedules" />
            <div className="text-sm text-slate-600">Empty page</div>
        </AdminLayout>
    );
}

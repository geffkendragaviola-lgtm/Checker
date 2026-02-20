import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

function MenuCard({ title, description, href }) {
    return (
        <Link
            href={href}
            className="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow"
        >
            <div className="flex items-start justify-between gap-4">
                <div>
                    <div className="text-sm font-semibold text-[#334155] group-hover:text-[#1E3A8A]">
                        {title}
                    </div>
                    <div className="mt-1 text-sm text-slate-500">{description}</div>
                </div>
                <div className="mt-0.5 text-slate-300 transition group-hover:text-[#1E3A8A]">
                    <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.69l-3.22-3.22a.75.75 0 111.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 11-1.06-1.06l3.22-3.22H3.75A.75.75 0 013 10z" clipRule="evenodd" />
                    </svg>
                </div>
            </div>
        </Link>
    );
}

export default function LettersIndex() {
    return (
        <AdminLayout title="Letters">
            <Head title="Letters" />

            <div className="mb-6">
                <h2 className="text-lg font-semibold text-[#334155]">Letters</h2>
                <p className="mt-1 text-sm text-slate-500">
                    Generate notice letters and view the letter history.
                </p>
            </div>

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <MenuCard
                    title="Generate Notice Letter"
                    description="Create a notice letter for lates and missing logs."
                    href={route('admin.letters.generate-notice')}
                />
                <MenuCard
                    title="Letter History"
                    description="View previously generated letters and their status."
                    href={route('admin.letters.history')}
                />
            </div>
        </AdminLayout>
    );
}

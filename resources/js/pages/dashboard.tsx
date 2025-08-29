import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    const { countPDF } = usePage<{ countPDF: number }>().props;
    const { countDOC } = usePage<{ countDOC: number }>().props;
    const { countIMG } = usePage<{ countIMG: number }>().props;
    const { countAll } = usePage<{ countAll: number }>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {/* PDF */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <div className="flex h-full w-full items-center justify-between rounded-xl bg-gradient-to-r from-pink-500 to-red-400 p-6 shadow-md">
                            <div>
                                <h2 className="text-2xl font-bold text-white">{countPDF}</h2>
                                <p className="text-sm text-white/80">Documents PDF</p>
                            </div>
                        </div>
                    </div>

                    {/* IMG */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <div className="flex h-full w-full items-center justify-between rounded-xl bg-gradient-to-r from-green-400 to-teal-300 p-6 shadow-md">
                            <div>
                                <h2 className="text-2xl font-bold text-white">{countIMG}</h2>
                                <p className="text-sm text-white/80">Documents IMG</p>
                            </div>
                        </div>
                    </div>

                    {/* DOC */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <div className="flex h-full w-full items-center justify-between rounded-xl bg-gradient-to-r from-indigo-500 to-blue-400 p-6 shadow-md">
                            <div>
                                <h2 className="text-2xl font-bold text-white">{countDOC}</h2>
                                <p className="text-sm text-white/80">Documents DOC</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* All Documents */}
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div className="flex h-full w-full items-center justify-between rounded-xl bg-gradient-to-r from-yellow-400 to-orange-300 p-6 shadow-md">
                        <div>
                            <h2 className="text-2xl font-bold text-white">{countAll}</h2>
                            <p className="text-sm text-white/80">Documents</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

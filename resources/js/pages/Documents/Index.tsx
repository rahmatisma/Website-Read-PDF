import TabSwitcher from '@/components/TabSwitcher';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import { useState, useEffect } from 'react';

// Import halaman isinya
import Pdf from './partials/Pdf';
import Gambar from './partials/Gambar';
import Doc from './partials/Doc';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Documents', href: '/documents' },
];

export default function Index() {
    const tabs = ['PDF', 'Gambar', 'Doc'];

    // Baca tab dari URL saat pertama load
    const getInitialTab = () => {
        const path = window.location.pathname.split('/').pop()?.toLowerCase();
        if (path && tabs.map(t => t.toLowerCase()).includes(path)) {
            return path.charAt(0).toUpperCase() + path.slice(1);
        }
        return 'PDF';
    };

    const [activeTab, setActiveTab] = useState(getInitialTab);

    const handleTabChange = (tab: string) => {
        setActiveTab(tab);
        window.history.pushState({}, '', `/documents/${tab.toLowerCase()}`);
    };

    // Render isi tab
    const renderContent = () => {
        switch (activeTab) {
            case 'PDF': return <Pdf />;
            case 'Gambar': return <Gambar />;
            case 'Doc': return <Doc />;
            default: return null;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Documents" />
            <TabSwitcher tabs={tabs} defaultTab={activeTab} onChange={handleTabChange} />
            <div className="mt-6">
                {renderContent()}
            </div>
        </AppLayout>
    );
}

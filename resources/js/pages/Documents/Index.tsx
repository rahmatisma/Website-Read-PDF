import TabSwitcher from '@/components/TabSwitcher';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Doc from './partials/Doc';
import Gambar from './partials/Gambar';
import Pdf from './partials/Pdf';

type PageProps = {
    documents?: any[];
    activeTab?: 'pdf' | 'gambar' | 'doc';
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Documents', href: '/documents' }];

const toLabel = (v: string) => {
    switch (v.toLowerCase()) {
        case 'pdf':
            return 'PDF';
        case 'gambar':
            return 'Gambar';
        case 'doc':
            return 'Doc';
        default:
            return 'PDF'; // default ke PDF
    }
};

const toValue = (label: string) => label.toLowerCase();

export default function Index() {
    // default activeTab kalau tidak dikirim → 'pdf'
    const { documents = [], activeTab = 'pdf' } = usePage<PageProps>().props;

    const tabs = ['PDF', 'Gambar', 'Doc'];
    const [currentTab, setCurrentTab] = useState<string>(toLabel(activeTab));

    useEffect(() => {
        setCurrentTab(toLabel(activeTab));
    }, [activeTab]);

    const handleTabChange = (label: string) => {
        setCurrentTab(label);
        const value = toValue(label);
        router.get(`/documents/${value}`);
    };

    const renderContent = () => {
        switch (activeTab) {
            case 'pdf':
                return <Pdf documents={documents} />;
            case 'gambar':
                return <Gambar documents={documents} />;
            case 'doc':
                return <Doc documents={documents} />;
            default:
                return <Pdf documents={documents} />; // fallback tetap PDF
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Documents" />
            <TabSwitcher
                tabs={tabs}
                defaultTab={currentTab}
                onChange={handleTabChange}
            />
            <div className="mt-6">{renderContent()}</div>
        </AppLayout>
    );
}

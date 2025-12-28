import TabSwitcher from '@/components/TabSwitcher';
import AppLayout from '@/layouts/app-layout';
import { Document } from '@/types/document';
import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Doc from './partials/Doc';
import Gambar from './partials/Gambar';
import Pdf from './partials/Pdf';
import FromChecklist from './partials/FormChecklist';

type PageProps = {
    documents?: Document[]; // ✅ Ganti any[] dengan Document[]
    activeTab?: 'pdf' | 'gambar' | 'doc' | 'form-checklist';
};

const toLabel = (v: string) => {
    switch (v.toLowerCase()) {
        case 'pdf':
            return 'PDF';
        case 'gambar':
            return 'Gambar';
        case 'doc':
            return 'Doc';
        case 'form-checklist':
            return 'Form Checklist';
        default:
            return 'PDF';
    }
};

const toValue = (label: string) => {
    const map: Record<string, string> = {
        PDF: 'pdf',
        Gambar: 'gambar',
        Doc: 'doc',
        'Form Checklist': 'form-checklist',
    };
    return map[label] || 'pdf';
};

export default function Index() {
    const { documents = [], activeTab = 'pdf' } = usePage<PageProps>().props;

    const tabs = ['PDF', 'Gambar', 'Doc', 'Form Checklist'];
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
            case 'form-checklist':
                return <FromChecklist documents={documents} />;
            default:
                return <Pdf documents={documents} />;
        }
    };

    return (
        <AppLayout>
            <Head title="Documents" />
            <TabSwitcher tabs={tabs} defaultTab={currentTab} onChange={handleTabChange} />
            <div className="mt-6">{renderContent()}</div>
        </AppLayout>
    );
}

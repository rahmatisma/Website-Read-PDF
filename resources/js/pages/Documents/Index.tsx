import TabSwitcher from '@/components/TabSwitcher';
import AppLayout from '@/layouts/app-layout';
import { Document } from '@/types/document';
import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Spk from './partials/SPK'; // ✅ Ganti dari Pdf
import FormChecklist from './partials/FormChecklist';
// import FormPmPop from './partials/FormPMPOP';

type PageProps = {
    documents?: Document[];
    activeTab?: 'spk' | 'form-checklist' | 'form-pm-pop'; // ✅ Update
    filters?: {
        keyword?: string;
        date_from?: string;
        date_to?: string;
    };
};

const toLabel = (v: string) => {
    switch (v.toLowerCase()) {
        case 'spk':
            return 'SPK';
        case 'form-checklist':
            return 'Form Checklist';
        case 'form-pm-pop':
            return 'Form PM POP';
        default:
            return 'SPK';
    }
};

const toValue = (label: string) => {
    const map: Record<string, string> = {
        'SPK': 'spk',
        'Form Checklist': 'form-checklist',
        'Form PM POP': 'form-pm-pop',
    };
    return map[label] || 'spk';
};

export default function Index() {
    const { documents = [], activeTab = 'spk', filters = {} } = usePage<PageProps>().props;

    const tabs = ['SPK', 'Form Checklist', 'Form PM POP']; // ✅ Update tabs
    const [currentTab, setCurrentTab] = useState<string>(toLabel(activeTab));

    useEffect(() => {
        setCurrentTab(toLabel(activeTab));
    }, [activeTab]);

    const handleTabChange = (label: string) => {
        setCurrentTab(label);
        const value = toValue(label);
        router.get(route('documents.filter', { type: value }));
    };

    const renderContent = () => {
        switch (activeTab) {
            case 'spk':
                return <Spk documents={documents} filters={filters} />;
            case 'form-checklist':
                return <FormChecklist documents={documents} filters={filters} />;
            // case 'form-pm-pop':
            //     return <FormPmPop documents={documents} filters={filters} />;
            default:
                return <Spk documents={documents} filters={filters} />;
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
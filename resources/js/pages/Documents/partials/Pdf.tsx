import DocumentTable from '@/components/DocumentTable';
import SearchFilter from '@/components/SearchFilter';
import UploadPDFBox from '@/components/UploadPDFBox';
import { Head } from '@inertiajs/react';

interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    created_at: string;
    file_size: string;
}

interface PdfProps {
    documents: Document[];
}

export default function Pdf({ documents }: PdfProps) {
    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    const formatFileSize = (size: number) => {
        if (size >= 1024 * 1024) {
            return (size / (1024 * 1024)).toFixed(2) + ' MB';
        } else {
            return (size / 1024).toFixed(2) + ' KB';
        }
    };

    return (
        <>
            <Head title="Daftar Dokumen PDF" />
            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadPDFBox />

                <div className="mt-6">
                    <h2 className="mb-4 text-xl font-semibold">Daftar Dokumen</h2>
                    {/* 🔎 Search & Filter */}
                    <SearchFilter />
                    {/* Tampilan dokumen */}
                    <DocumentTable documents={documents} type="pdf" />
                </div>
            </div>
        </>
    );
}

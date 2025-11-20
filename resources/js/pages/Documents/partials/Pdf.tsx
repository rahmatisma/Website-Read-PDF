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
    return (
        <>
            <Head title="Daftar Dokumen PDF" />

            <div className="space-y-6 p-6">
                
                {/* Upload Box */}
                <UploadPDFBox />

                {/* Daftar Dokumen */}
                <div className="mt-6">
                    <h2 className="mb-4 text-xl font-semibold">Daftar Dokumen</h2>

                    {/* Search */}
                    <SearchFilter />

                    {/* Table */}
                    <DocumentTable documents={documents} type="pdf" />
                </div>
            </div>
        </>
    );
}

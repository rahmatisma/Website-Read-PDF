import DocumentTable from '@/components/DocumentTable';
import SearchFilter from '@/components/SearchFilter';
import UploadGambarBox from '@/components/UploadGambarBox';
import { Head } from '@inertiajs/react';

interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    created_at: string;
    file_size: string;
}

interface GambarProps {
    documents: Document[];
}

export default function Gambar({ documents }: GambarProps) {
    return (
        <>
            <Head title="Daftar Gambar" />

            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadGambarBox />

                {/* Daftar Dokumen */}
                <div className="mt-6">
                    <h2 className="mb-4 text-xl font-semibold">Daftar Gambar</h2>

                    {/* Search & Filter */}
                    <SearchFilter />

                    {/* Table */}
                    <DocumentTable documents={documents} type="image" />
                </div>
            </div>
        </>
    );
}

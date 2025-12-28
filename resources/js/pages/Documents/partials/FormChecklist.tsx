import DocumentTable from '@/components/DocumentTable';
import SearchFilter from '@/components/SearchFilter';
import UploadChecklistBox from '@/components/UploadChecklistBox';
import { Document } from '@/types/document';
import { Head, router } from '@inertiajs/react';
import { toast } from 'sonner';

interface PdfProps {
    documents: Document[];
}

export default function Checklist({ documents }: PdfProps) {
    const handleDelete = (id: number) => {
        toast.warning('Hapus dokumen?', {
            description: 'Dokumen yang dihapus tidak dapat dikembalikan.',
            classNames: {
                toast: '!bg-gray-900 !border-2 !border-purple-400',
                title: '!text-white',
                description: '!text-white',
                icon: '!text-yellow-500',
                actionButton: '!bg-gradient-to-r !from-red-500 !to-pink-500 hover:!brightness-110 !text-white !font-bold',
                cancelButton: '!bg-gray-700 hover:!bg-gray-600 !text-white',
            },
            action: {
                label: 'Hapus',
                onClick: () => {
                    router.delete(route('documents.destroy', id), {
                        onSuccess: () => {
                            toast.success('Dokumen berhasil dihapus!');
                        },
                        onError: () => {
                            toast.error('Gagal menghapus dokumen');
                        },
                    });
                },
            },
            cancel: {
                label: 'Batal',
                onClick: () => {},
            },
        });
    };

    return (
        <>
            <Head title="Daftar Dokumen Form Checklist" />

            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadChecklistBox />

                {/* Daftar Dokumen */}
                <div className="mt-6">
                    <h2 className="mb-4 text-xl font-semibold">Daftar Dokumen Form Checklist</h2>

                    {/* Search */}
                    <SearchFilter />

                    {/* Table - KIRIM FUNGSI handleDelete */}
                    <DocumentTable documents={documents} type="pdf" onDelete={handleDelete} />
                </div>
            </div>
        </>
    );
}
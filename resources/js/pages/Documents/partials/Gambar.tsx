import DocumentTable from '@/components/DocumentTable';
import SearchFilter from '@/components/SearchFilter';
import UploadGambarBox from '@/components/UploadGambarBox';
import { Head, router } from '@inertiajs/react';
import { toast } from 'sonner';
import { Document } from '@/types/document';

interface PdfProps {
    documents: Document[];
}

interface GambarProps {
    documents: Document[];
}

export default function Gambar({ documents }: GambarProps) {
    const handleDelete = (id: number) => {
        toast.warning('Hapus gambar?', {
            description: 'Gambar yang dihapus tidak dapat dikembalikan.',
            classNames: {
                toast: '!bg-gray-900 !border-2 !border-cyan-400',
                title: '!text-white',
                description: '!text-white',
                icon: '!text-yellow-500',
                actionButton: '!bg-gradient-to-r !from-blue-500 !to-green-500 hover:!brightness-110 !text-white !font-bold',
                cancelButton: '!bg-gray-700 hover:!bg-gray-600 !text-white',
            },
            action: {
                label: 'Hapus',
                onClick: () => {
                    router.delete(route('documents.destroy', id), {
                        onSuccess: () => {
                            toast.success('Gambar berhasil dihapus!');
                        },
                        onError: () => {
                            toast.error('Gagal menghapus gambar');
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
                    <DocumentTable documents={documents} type="image" onDelete={handleDelete} />
                </div>
            </div>
        </>
    );
}
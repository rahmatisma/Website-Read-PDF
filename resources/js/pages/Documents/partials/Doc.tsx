import DocumentTable from '@/components/DocumentTable';
import SearchFilter from '@/components/SearchFilter';
import UploadDocBox from '@/components/UploadDocBox';
import { Head, router } from '@inertiajs/react';
import { toast } from 'sonner';

interface Document {
    id_upload: number;
    file_name: string;
    file_path: string;
    created_at: string;
    file_size: string;
}

interface DocProps {
    documents: Document[];
}

export default function Doc({ documents }: DocProps) {
    const handleDelete = (id: number) => {
        toast.warning('Hapus dokumen?', {
            description: 'Dokumen yang dihapus tidak dapat dikembalikan.',
            classNames: {
                toast: '!bg-gray-900 !border-2 !border-indigo-400',
                title: '!text-white',
                description: '!text-white',
                icon: '!text-yellow-500',
                actionButton: '!bg-gradient-to-r !from-indigo-500 !to-purple-500 hover:!brightness-110 !text-white !font-bold',
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
            <Head title="Daftar Dokumen" />
            <div className="space-y-6 p-6">
                {/* Upload Box */}
                <UploadDocBox />

                <div className="mt-6">
                    <h2 className="mb-4 text-xl font-semibold">Daftar Dokumen</h2>
                    {/* 🔎 Search & Filter */}
                    <SearchFilter />
                    {/* Tampilan dokumen */}
                    <DocumentTable documents={documents} type="doc" onDelete={handleDelete} />
                </div>
            </div>
        </>
    );
}